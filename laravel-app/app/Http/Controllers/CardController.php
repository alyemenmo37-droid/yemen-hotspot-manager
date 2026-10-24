<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    /**
     * عرض كل البطاقات
     */
    public function index()
    {
        $cards = Card::orderByDesc('created_at')->paginate(15);
        
        // إحصائيات
        $stats = [
            'total' => Card::count(),
            'active' => Card::where('status', Card::STATUS_ACTIVE)->count(),
            'unused' => Card::where('status', Card::STATUS_UNUSED)->count(),
            'sales' => Card::where('status', '!=', Card::STATUS_UNUSED)->sum('amount'),
        ];
        
        return view('cards.index', compact('cards', 'stats'));
    }
    
    /**
     * نموذج إضافة بطاقة
     */
    public function create()
    {
        return view('cards.create');
    }
    
    /**
     * حفظ بطاقة جديدة
     */
    public function store(Request $request)
    {
        // التحقق
        $validated = $request->validate([
            'number' => [
                'required',
                'digits:10',
                'unique:cards,number',
            ],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000'],
            'speed' => ['required', 'in:512k/512k,256k/1M,512k/2M,2M/15M'],
            'data_limit_mb' => ['nullable', 'integer', 'min:0'],
        ], [
            'number.required' => 'رقم البطاقة مطلوب',
            'number.digits' => 'رقم البطاقة يجب أن يكون 10 أرقام',
            'number.unique' => 'رقم البطاقة موجود مسبقاً',
            'amount.required' => 'السعر مطلوب',
            'amount.min' => 'السعر يجب أن يكون 1 على الأقل',
            'speed.required' => 'السرعة مطلوبة',
            'speed.in' => 'السرعة غير صالحة',
        ]);
        
        $validated['status'] = Card::STATUS_UNUSED;
        
        Card::create($validated);
        
        return redirect()
            ->route('cards.index')
            ->with('success', "تم إضافة البطاقة {$validated['number']}");
    }
    
    /**
     * عرض بطاقة واحدة
     */
    public function show(Card $card)
    {
        return view('cards.show', compact('card'));
    }
    
    /**
     * نموذج تعديل
     */
    public function edit(Card $card)
    {
        return view('cards.edit', compact('card'));
    }
    
    /**
     * حفظ التعديلات
     */
    public function update(Request $request, Card $card)
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'speed' => ['required', 'in:512k/512k,256k/1M,512k/2M,2M/15M'],
            'status' => ['required', 'in:unused,active,expired,stopped'],
        ]);
        
        $card->update($validated);
        
        return redirect()
            ->route('cards.index')
            ->with('success', "تم تعديل البطاقة {$card->number}");
    }
    
    /**
     * حذف
     */
    public function destroy(Card $card)
    {
        $number = $card->number;
        $card->delete();
        
        return redirect()
            ->route('cards.index')
            ->with('success', "تم حذف البطاقة $number");
    }
    
    /**
     * تفعيل بطاقة (إجراء إضافي)
     */
    public function activate(Card $card)
    {
        $card->activate();
        
        return redirect()
            ->route('cards.index')
            ->with('success', "تم تفعيل البطاقة {$card->number}");
    }
}
