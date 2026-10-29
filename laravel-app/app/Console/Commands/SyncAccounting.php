<?php

namespace App\Console\Commands;

use App\Services\Radius\RadiusService;
use Illuminate\Console\Command;

class SyncAccounting extends Command
{
    protected $signature = 'radius:sync-accounting';
    protected $description = 'مزامنة استهلاك البيانات من RADIUS إلى Cards';

    public function handle(RadiusService $radius): int
    {
        $this->info('📊 مزامنة استهلاك البيانات...');
        $this->newLine();
        
        $stats = $radius->syncDataUsage();
        
        $this->info("✅ تم تحديث: {$stats['updated']} بطاقة");
        
        if ($stats['exceeded'] > 0) {
            $this->warn("⚠️  تجاوزوا الحد: {$stats['exceeded']}");
        }
        
        if ($stats['errors'] > 0) {
            $this->error("❌ أخطاء: {$stats['errors']}");
        }
        
        // الإحصائيات العامة
        $overall = $radius->getOverallStats();
        
        $this->newLine();
        $this->info('📊 الإحصائيات:');
        $this->line("   الجلسات: {$overall['total_sessions']}");
        $this->line("   المستخدمون: {$overall['unique_users']}");
        $this->line("   التحميل: {$overall['download_gb']} GB");
        $this->line("   الرفع: {$overall['upload_gb']} GB");
        $this->line("   الإجمالي: {$overall['total_gb']} GB");
        
        return 0;
    }
}
