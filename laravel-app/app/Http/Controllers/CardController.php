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

    public function store(Request $request, MikroTikService $mikrotik)
    {
        $validated = $request->validate([
            'number' => ['required', 'digits:10', 'unique:cards,number'],
            'speed_level_id' => ['required', 'exists:speed_levels,id'],
            'data_limit_mb' => ['nullable', 'integer', 'min:0'],
        ], [
            'number.required' => 'رقم البطاقة مطلوب',
            'number.digits' => 'رقم البطاقة يجب أن يكون 10 أرقام',
            'number.unique' => 'رقم البطاقة موجود مسبقاً',
            'speed_level_id.required' => 'السرعة مطلوبة',
        ]);

        $speedLevel = SpeedLevel::findOrFail($validated['speed_level_id']);

        // 1. إضافة إلى MikroTik
        $duration = '1d'; // يمكن حسابها من speedLevel
        $mikrotikResult = $mikrotik->addUser(
            $validated['number'],
            $speedLevel->rate_limit,
            $duration
        );

        if (! $mikrotikResult['success']) {
            return back()
                ->withInput()
                ->with('error', 'فشل MikroTik: '.$mikrotikResult['message']);
        }

        // 2. حفظ في قاعدة البيانات
        Card::create([
            'number' => $validated['number'],
            'amount' => $speedLevel->price,
            'speed' => $speedLevel->rate_limit,
            'speed_level_id' => $speedLevel->id,
            'status' => Card::STATUS_UNUSED,
            'data_limit_mb' => $validated['data_limit_mb'] ?? $speedLevel->data_limit_mb,
        ]);

        return redirect()
            ->route('cards.index')
            ->with('success', "تم إضافة البطاقة {$validated['number']} (MikroTik: {$mikrotikResult['mode']})");
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
