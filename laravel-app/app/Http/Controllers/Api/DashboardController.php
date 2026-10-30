<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Services\Radius\RadiusService;

class DashboardController extends Controller
{
    /**
     * إحصائيات Dashboard
     * 
     * GET /api/dashboard
     */
    public function index(RadiusService $radius)
    {
        $stats = [
            'cards' => [
                'total' => Card::count(),
                'active' => Card::where('status', 'active')->count(),
                'unused' => Card::where('status', 'unused')->count(),
                'sales' => Card::where('status', '!=', 'unused')->sum('amount'),
            ],
            'accounting' => $radius->getOverallStats(),
            'recent_cards' => Card::with('speedLevel.category')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
    
    /**
     * معلومات MikroTik
     * 
     * GET /api/dashboard/mikrotik
     */
    public function mikrotik(\App\Services\MikroTikService $mikrotik)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'info' => $mikrotik->getSystemInfo(),
                'sessions' => $mikrotik->getActiveSessions(),
                'sessions_count' => count($mikrotik->getActiveSessions()),
            ],
        ]);
    }
}
