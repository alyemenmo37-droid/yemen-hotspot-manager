<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\SpeedLevel;
use App\Services\MikroTikService;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $query = Card::with('speedLevel.category')
            ->where('tenant_id', auth()->user()->tenant_id); // ← فلترة

        // ═══════════════════════════════════════
        // 1. البحث المتقدم
        // ═══════════════════════════════════════
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%");
            });
        }

        // ═══════════════════════════════════════
        // 2. فلترة متقدمة
        // ═══════════════════════════════════════

        // الحالة
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // الفئة
        if ($categoryId = $request->input('category_id')) {
            $query->whereHas('speedLevel', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // السعر (من - إلى)
        if ($min = $request->input('min_price')) {
            $query->where('amount', '>=', (int) $min);
        }
        if ($max = $request->input('max_price')) {
            $query->where('amount', '<=', (int) $max);
        }

        // ═══════════════════════════════════════
        // 3. الترتيب
        // ═══════════════════════════════════════
        $sort = $request->input('sort', 'created_at');
        $order = $request->input('order', 'desc');
        $query->orderBy($sort, $order);

        $cards = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Card::count(),
            'active' => Card::where('status', Card::STATUS_ACTIVE)->count(),
            'unused' => Card::where('status', Card::STATUS_UNUSED)->count(),
            'sales' => Card::where('status', '!=', Card::STATUS_UNUSED)->sum('amount'),
        ];

        $categories = Category::all();

        return view('cards.index', compact('cards', 'stats', 'categories'));
    }

    public function create()
    {
        $speedLevels = SpeedLevel::with('category')
            ->where('is_active', true)
            ->get()
            ->groupBy('category.name');

        return view('cards.create', compact('speedLevels'));
    }

    public function store(Request $request, MikroTikService $mikrotik, TelegramService $telegram)
    {
        $validated = $request->validate([
            'number' => ['required', 'digits:10', 'unique:cards,number'],
            'speed_level_id' => ['required', 'exists:speed_levels,id'],
        ]);

        $speedLevel = SpeedLevel::findOrFail($validated['speed_level_id']);

        // MikroTik
        $result = $mikrotik->addUser($validated['number'], $speedLevel->rate_limit);
        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        // DB
        $card = Card::create([
            'number' => $validated['number'],
            'amount' => $speedLevel->price,
            'speed' => $speedLevel->rate_limit,
            'speed_level_id' => $speedLevel->id,
            'status' => Card::STATUS_UNUSED,
            'data_limit_mb' => $speedLevel->data_limit_mb,
            'tenant_id' => auth()->user()->tenant_id, // ← جديد
        ]);

        // إشعار Telegram
        $telegram->notifyNewCard($card->number, $card->amount, $card->speed);

        // فحص المخزون
        $this->checkLowStock($telegram);

        return redirect()
            ->route('cards.index')
            ->with('success', "تم إضافة {$card->number}");
    }

    private function checkLowStock(TelegramService $telegram): void
    {
        $threshold = config('notifications.low_stock_threshold', 10);
        $unused = Card::where('status', Card::STATUS_UNUSED)->count();

        if ($unused <= $threshold) {
            $telegram->notifyLowStock($unused, $threshold);
        }
    }

    public function show(Card $card)
    {
        $card->load('speedLevel.category');

        return view('cards.show', compact('card'));
    }

    public function edit(Card $card)
    {
        $speedLevels = SpeedLevel::with('category')
            ->where('is_active', true)
            ->get()
            ->groupBy('category.name');

        return view('cards.edit', compact('card', 'speedLevels'));
    }

    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'speed_level_id' => ['required', 'exists:speed_levels,id'],
            'status' => ['required', 'in:unused,active,expired,stopped'],
        ]);

        $speedLevel = SpeedLevel::findOrFail($validated['speed_level_id']);

        $card->update([
            'speed_level_id' => $speedLevel->id,
            'speed' => $speedLevel->rate_limit,
            'amount' => $speedLevel->price,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('cards.index')
            ->with('success', "تم تعديل البطاقة {$card->number}");
    }

    public function destroy(Card $card, MikroTikService $mikrotik)
    {
        $number = $card->number;

        // 1. حذف من MikroTik
        $mikrotik->removeUser($number);

        // 2. حذف من radacct (اختياري)
        \DB::table('radacct')->where('username', $number)->delete();

        // 2. حذف من DB
        $card->delete();

        return redirect()
            ->route('cards.index')
            ->with('success', "تم حذف البطاقة $number");
    }

    public function activate(Card $card)
    {
        $card->activate();

        return redirect()
            ->route('cards.index')
            ->with('success', "تم تفعيل البطاقة {$card->number}");
    }

    /**
     * إجراء جماعي
     */
    public function bulkAction(Request $request, MikroTikService $mikrotik)
    {
        $validated = $request->validate([
            'action' => ['required', 'in:activate,delete,expire'],
            'cards' => ['required', 'array'],
            'cards.*' => ['integer', 'exists:cards,id'],
        ]);

        $ids = $validated['cards'];
        $action = $validated['action'];

        $cards = Card::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($cards as $card) {
            try {
                switch ($action) {
                    case 'activate':
                        if ($card->isAvailable()) {
                            $card->activate();
                            $count++;
                        }
                        break;

                    case 'delete':
                        $mikrotik->removeUser($card->number);
                        $card->delete();
                        $count++;
                        break;

                    case 'expire':
                        if ($card->status !== Card::STATUS_EXPIRED) {
                            $card->expire();
                            $count++;
                        }
                        break;
                }
            } catch (\Throwable $e) {
                \Log::error("Bulk {$action} failed for {$card->number}", ['error' => $e->getMessage()]);
            }
        }

        $actionLabels = [
            'activate' => 'تفعيل',
            'delete' => 'حذف',
            'expire' => 'إنهاء',
        ];

        return back()->with('success', "تم {$actionLabels[$action]} {$count} بطاقة");
    }
}
