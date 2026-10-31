<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\SpeedLevel;
use App\Services\MikroTikService;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::with('speedLevel.category')
            ->orderByDesc('created_at')
            ->paginate(15);

        $stats = [
            'total' => Card::count(),
            'active' => Card::where('status', Card::STATUS_ACTIVE)->count(),
            'unused' => Card::where('status', Card::STATUS_UNUSED)->count(),
            'sales' => Card::where('status', '!=', Card::STATUS_UNUSED)->sum('amount'),
        ];

        return view('cards.index', compact('cards', 'stats'));
    }

    public function create()
    {
        if (! auth()->user()->isAdmin() && auth()->user()->role !== 'manager') {
            abort(403, 'غير مصرح بإضافة بطاقات');
        }

        $speedLevels = SpeedLevel::with('category')
            ->where('is_active', true)
            ->get()
            ->groupBy('category.name');

        return view('cards.create', compact('speedLevels'));
    }
public function store(Request $request, \App\Services\MikroTikService $mikrotik, \App\Services\TelegramService $telegram)
{
    $validated = $request->validate([
        'number' => ['required', 'digits:10', 'unique:cards,number'],
        'speed_level_id' => ['required', 'exists:speed_levels,id'],
    ]);
    
    $speedLevel = SpeedLevel::findOrFail($validated['speed_level_id']);
    
    // MikroTik
    $result = $mikrotik->addUser($validated['number'], $speedLevel->rate_limit);
    if (!$result['success']) {
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
    ]);
    
    // إشعار Telegram
    $telegram->notifyNewCard($card->number, $card->amount, $card->speed);
    
    // فحص المخزون
    $this->checkLowStock($telegram);
    
    return redirect()
        ->route('cards.index')
        ->with('success', "تم إضافة {$card->number}");
}

private function checkLowStock(\App\Services\TelegramService $telegram): void
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
}
