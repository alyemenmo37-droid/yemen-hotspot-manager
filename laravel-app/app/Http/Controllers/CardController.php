<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CardController extends Controller
{
    /**
     * عرض كل البطاقات
     */
    public function index()
    {
        // بيانات تجريبية (مؤقتاً، حتى نضيف DB)
        $cards = [
            ['id' => 1, 'number' => '1234567890', 'amount' => 250, 'status' => 'active'],
            ['id' => 2, 'number' => '0987654321', 'amount' => 500, 'status' => 'unused'],
            ['id' => 3, 'number' => '1122334455', 'amount' => 1000, 'status' => 'expired'],
        ];
        
        return view('cards.index', compact('cards'));
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
        // سيتم تطويره لاحقاً
        return redirect()->route('cards.index')
            ->with('success', 'تم إضافة البطاقة');
    }
    
    /**
     * عرض بطاقة واحدة
     */
    public function show(string $id)
    {
        return view('cards.show', compact('id'));
    }
    
    /**
     * نموذج تعديل
     */
    public function edit(string $id)
    {
        return view('cards.edit', compact('id'));
    }
    
    /**
     * حفظ التعديلات
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('cards.index')
            ->with('success', 'تم تعديل البطاقة');
    }
    
    /**
     * حذف
     */
    public function destroy(string $id)
    {
        return redirect()->route('cards.index')
            ->with('success', 'تم حذف البطاقة');
    }
}
