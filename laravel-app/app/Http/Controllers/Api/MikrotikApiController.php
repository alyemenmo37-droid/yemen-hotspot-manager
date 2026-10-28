<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\MikrotikLog;
use App\Services\Mikrotik\MockManager;
use App\Services\MikroTikService;
use Illuminate\Http\JsonResponse;

class MikrotikApiController extends Controller
{
    public function __construct(
        private MikroTikService $mikrotik
    ) {}

    /**
     * حالة النظام (للتحديث الحي)
     */
    public function status(): JsonResponse
    {
        $info = $this->mikrotik->getSystemInfo();
        $sessions = $this->mikrotik->getActiveSessions();

        return response()->json([
            'success' => true,
            'mode' => $this->mikrotik->isMock() ? 'mock' : 'real',
            'info' => $info,
            'sessions' => $sessions,
            'sessions_count' => count($sessions),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * إحصائيات سريعة
     */
    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'stats' => [
                'total_cards' => Card::count(),
                'active_cards' => Card::where('status', 'active')->count(),
                'unused_cards' => Card::where('status', 'unused')->count(),
                'total_sales' => Card::where('status', '!=', 'unused')->sum('amount'),
                'mikrotik_users' => MockManager::class
                    ? app(MockManager::class)->getUsersCount()
                    : 0,
            ],
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * آخر العمليات
     */
    public function recentLogs(): JsonResponse
    {
        $logs = MikrotikLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'action' => $log->action,
                'icon' => $log->icon,
                'card_number' => $log->card_number,
                'status' => $log->status,
                'message' => $log->message,
                'mode' => $log->mode,
                'user_name' => $log->user?->name ?? 'مجهول',
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'ago' => $log->created_at->diffForHumans(),
            ]);

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }
}
