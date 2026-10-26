<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\SpeedLevel;
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
        if (!auth()->user()->isAdmin() && auth()->user()->role !== 'manager') {
            abort(403, 'غير مصرح بإضافة بطاقات');
        }
        
        $speedLevels = SpeedLevel::with('category')
            ->where('is_active', true)
            ->get()
            ->groupBy('category.name');
        
        return view('cards.create', compact('speedLevels'));
    }
    
    public function store(Request $request)
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
            'speed_level_id.exists' => 'السرعة غير موجودة',
        ]);
        
        // جلب speed level للحصول على السعر
        $speedLevel = SpeedLevel::findOrFail($validated['speed_level_id']);
        
        // إنشاء البطاقة
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
            ->with('success', "تم إضافة البطاقة {$validated['number']} (السعر: {$speedLevel->price} ريال)");
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
    
    public function destroy(Card $card)
    {
        $number = $card->number;
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
