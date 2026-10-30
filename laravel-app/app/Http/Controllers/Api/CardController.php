<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\SpeedLevel;
use Illuminate\Http\Request;

class CardController extends Controller
{
    /**
     * قائمة البطاقات
     * 
     * GET /api/cards
     */
    public function index(Request $request)
    {
        $query = Card::with('speedLevel.category');
        
        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // بحث
        if ($request->has('search')) {
            $query->where('number', 'like', "%{$request->search}%");
        }
        
        $cards = $query->orderByDesc('created_at')->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $cards->items(),
            'meta' => [
                'current_page' => $cards->currentPage(),
                'per_page' => $cards->perPage(),
                'total' => $cards->total(),
                'last_page' => $cards->lastPage(),
            ],
        ]);
    }
    
    /**
     * بطاقة واحدة
     * 
     * GET /api/cards/{card}
     */
    public function show(Card $card)
    {
        $card->load('speedLevel.category');
        
        return response()->json([
            'success' => true,
            'data' => $card,
        ]);
    }
    
    /**
     * إضافة بطاقة
     * 
     * POST /api/cards
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => ['required', 'digits:10', 'unique:cards,number'],
            'speed_level_id' => ['required', 'exists:speed_levels,id'],
        ], [
            'number.required' => 'رقم البطاقة مطلوب',
            'number.digits' => 'رقم البطاقة: 10 أرقام',
            'number.unique' => 'البطاقة موجودة',
            'speed_level_id.required' => 'السرعة مطلوبة',
        ]);
        
        $speedLevel = SpeedLevel::findOrFail($validated['speed_level_id']);
        
        // مزامنة مع MikroTik + RADIUS
        $mikrotik = app(\App\Services\MikroTikService::class);
        $result = $mikrotik->addUser($validated['number'], $speedLevel->rate_limit);
        
        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 500);
        }
        
        $card = Card::create([
            'number' => $validated['number'],
            'amount' => $speedLevel->price,
            'speed' => $speedLevel->rate_limit,
            'speed_level_id' => $speedLevel->id,
            'status' => Card::STATUS_UNUSED,
            'data_limit_mb' => $speedLevel->data_limit_mb,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'تم إضافة البطاقة',
            'data' => $card->load('speedLevel.category'),
        ], 201);
    }
    
    /**
     * تفعيل بطاقة
     * 
     * POST /api/cards/{card}/activate
     */
    public function activate(Card $card)
    {
        if (!$card->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'البطاقة غير متاحة للتفعيل',
            ], 400);
        }
        
        $card->activate();
        
        return response()->json([
            'success' => true,
            'message' => 'تم تفعيل البطاقة',
            'data' => $card,
        ]);
    }
    
    /**
     * حذف بطاقة
     * 
     * DELETE /api/cards/{card}
     */
    public function destroy(Card $card)
    {
        $number = $card->number;
        
        // حذف من MikroTik + RADIUS
        $mikrotik = app(\App\Services\MikroTikService::class);
        $mikrotik->removeUser($number);
        
        // حذف من DB
        $card->delete();
        
        return response()->json([
            'success' => true,
            'message' => "تم حذف {$number}",
        ]);
    }
    
    /**
     * إحصائيات البطاقات
     * 
     * GET /api/cards/stats
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => Card::count(),
                'active' => Card::where('status', 'active')->count(),
                'unused' => Card::where('status', 'unused')->count(),
                'expired' => Card::where('status', 'expired')->count(),
                'sales' => Card::where('status', '!=', 'unused')->sum('amount'),
            ],
        ]);
    }
}
