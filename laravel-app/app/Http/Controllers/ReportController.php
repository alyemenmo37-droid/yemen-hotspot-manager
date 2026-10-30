<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Category;
use App\Services\Radius\RadiusService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * صفحة التقارير
     */
    public function index()
    {
        return view('reports.index');
    }
    
    /**
     * تقرير PDF: البطاقات
     */
    public function cardsPdf(Request $request)
    {
        $status = $request->input('status', 'all');
        
        $query = Card::with('speedLevel.category');
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $cards = $query->orderByDesc('created_at')->get();
        
        $stats = [
            'total' => $cards->count(),
            'active' => $cards->where('status', 'active')->count(),
            'sales' => $cards->where('status', '!=', 'unused')->sum('amount'),
        ];
        
        $pdf = Pdf::loadView('reports.cards-pdf', compact('cards', 'stats', 'status'));
        $pdf->setPaper('A4', 'landscape');
        
        $filename = 'cards_' . now()->format('Y-m-d_H-i') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * تقرير PDF: المبيعات
     */
    public function salesPdf()
    {
        // مبيعات حسب الفئة
        $byCategory = Category::select('categories.*')
            ->selectRaw('
                COUNT(DISTINCT cards.id) as cards_count,
                COALESCE(SUM(cards.amount), 0) as total_sales
            ')
            ->leftJoin('speed_levels', 'speed_levels.category_id', '=', 'categories.id')
            ->leftJoin('cards', function ($join) {
                $join->on('cards.speed_level_id', '=', 'speed_levels.id')
                     ->where('cards.status', '!=', 'unused');
            })
            ->groupBy('categories.id')
            ->get();
        
        // مبيعات يومية (آخر 30 يوم)
        $dailySales = Card::selectRaw('
            DATE(created_at) as date,
            COUNT(*) as count,
            SUM(CASE WHEN status != "unused" THEN amount ELSE 0 END) as revenue
        ')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // الإجماليات
        $totals = [
            'cards' => Card::count(),
            'sold' => Card::where('status', '!=', 'unused')->count(),
            'revenue' => Card::where('status', '!=', 'unused')->sum('amount'),
        ];
        
        $pdf = Pdf::loadView('reports.sales-pdf', compact('byCategory', 'dailySales', 'totals'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'sales_' . now()->format('Y-m-d_H-i') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * تقرير PDF: الاستهلاك
     */
    public function usagePdf(RadiusService $radius)
    {
        $overall = $radius->getOverallStats();
        $topConsumers = $radius->getTopConsumers(30);
        
        $pdf = Pdf::loadView('reports.usage-pdf', compact('overall', 'topConsumers'));
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'usage_' . now()->format('Y-m-d_H-i') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    /**
     * تصدير CSV: البطاقات
     */
    public function cardsCsv()
    {
        $cards = Card::with('speedLevel.category')
            ->orderByDesc('created_at')
            ->get();
        
        $filename = 'cards_' . now()->format('Y-m-d_H-i') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        return response()->streamDownload(function () use ($cards) {
            $handle = fopen('php://output', 'w');
            
            // BOM (لدعم UTF-8 في Excel)
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Header
            fputcsv($handle, [
                'ID',
                'الرقم',
                'السعر',
                'السرعة',
                'الحالة',
                'الفئة',
                'حد البيانات (MB)',
                'المستخدم (MB)',
                'تاريخ الإنشاء',
                'تاريخ التفعيل',
            ]);
            
            // Data
            foreach ($cards as $card) {
                fputcsv($handle, [
                    $card->id,
                    $card->number,
                    $card->amount,
                    $card->speed,
                    $card->status,
                    $card->speedLevel?->category?->name ?? '-',
                    $card->data_limit_mb ?? '-',
                    $card->data_used_mb,
                    $card->created_at?->format('Y-m-d H:i') ?? '-',
                    $card->activated_at?->format('Y-m-d H:i') ?? '-',
                ]);
            }
            
            fclose($handle);
        }, $filename, $headers);
    }
    
    /**
     * تصدير CSV: الاستهلاك
     */
    public function usageCsv(RadiusService $radius)
    {
        $topConsumers = $radius->getTopConsumers(100);
        
        $filename = 'usage_' . now()->format('Y-m-d_H-i') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        return response()->streamDownload(function () use ($topConsumers) {
            $handle = fopen('php://output', 'w');
            
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($handle, [
                'البطاقة',
                'الاستهلاك (MB)',
                'الاستهلاك (GB)',
                'عدد الجلسات',
            ]);
            
            foreach ($topConsumers as $consumer) {
                $mb = round($consumer->total_bytes / 1024 / 1024, 2);
                $gb = round($consumer->total_bytes / 1024 / 1024 / 1024, 2);
                
                fputcsv($handle, [
                    $consumer->username,
                    $mb,
                    $gb,
                    $consumer->sessions_count,
                ]);
            }
            
            fclose($handle);
        }, $filename, $headers);
    }
}
