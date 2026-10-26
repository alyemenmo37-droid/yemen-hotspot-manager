<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Models\SpeedLevel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * صفحة الإحصائيات
     */
    public function index()
    {
        // ═══════════════════════════════════════
        // 1. إحصائيات عامة
        // ═══════════════════════════════════════
        $general = [
            'total_cards' => Card::count(),
            'active_cards' => Card::where('status', 'active')->count(),
            'unused_cards' => Card::where('status', 'unused')->count(),
            'expired_cards' => Card::where('status', 'expired')->count(),
            'total_sales' => Card::where('status', '!=', 'unused')->sum('amount'),
            'total_users' => User::count(),
        ];
        
        // ═══════════════════════════════════════
        // 2. المبيعات حسب الفئة
        // ═══════════════════════════════════════
        $salesByCategory = Category::select('categories.*')
            ->selectRaw('
                COUNT(DISTINCT cards.id) as cards_count,
                COALESCE(SUM(cards.amount), 0) as total_sales,
                COALESCE(AVG(cards.amount), 0) as avg_price
            ')
            ->leftJoin('speed_levels', 'speed_levels.category_id', '=', 'categories.id')
            ->leftJoin('cards', function ($join) {
                $join->on('cards.speed_level_id', '=', 'speed_levels.id')
                     ->where('cards.status', '!=', 'unused');
            })
            ->groupBy('categories.id', 'categories.name', 'categories.slug', 'categories.color', 'categories.description', 'categories.is_active', 'categories.created_at', 'categories.updated_at')
            ->get();
        
        // ═══════════════════════════════════════
        // 3. المبيعات اليومية (آخر 7 أيام)
        // ═══════════════════════════════════════
        $dailySales = Card::selectRaw('
            DATE(created_at) as date,
            COUNT(*) as count,
            SUM(CASE WHEN status != "unused" THEN amount ELSE 0 END) as revenue
        ')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // ═══════════════════════════════════════
        // 4. أعلى 5 بطاقات
        // ═══════════════════════════════════════
        $topCards = Card::with('speedLevel.category')
            ->where('status', '!=', 'unused')
            ->orderByDesc('amount')
            ->limit(5)
            ->get();
        
        // ═══════════════════════════════════════
        // 5. توزيع الحالات
        // ═══════════════════════════════════════
        $statusDistribution = Card::selectRaw('
            status, 
            COUNT(*) as count
        ')
            ->groupBy('status')
            ->get();
        
        return view('statistics.index', compact(
            'general',
            'salesByCategory',
            'dailySales',
            'topCards',
            'statusDistribution'
        ));
    }
}
