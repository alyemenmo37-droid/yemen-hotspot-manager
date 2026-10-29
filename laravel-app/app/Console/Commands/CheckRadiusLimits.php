<?php

namespace App\Console\Commands;

use App\Services\Radius\CoaClient;
use App\Services\Radius\RadiusService;
use Illuminate\Console\Command;

class CheckRadiusLimits extends Command
{
    protected $signature = 'radius:check-limits {--dry-run : عرض فقط بدون تنفيذ}';
    protected $description = 'فحص الجلسات المنتهية وفصلها';

    public function handle(RadiusService $radius, CoaClient $coa): int
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 فحص الجلسات...');
        $this->newLine();
        
        // ═══════════════════════════════════════
        // 1. الجلسات المنتهية زمنياً
        // ═══════════════════════════════════════
        $expired = $radius->getExpiredSessions();
        
        if (empty($expired)) {
            $this->info('✅ لا توجد جلسات منتهية زمنياً');
        } else {
            $this->warn('⏰ ' . count($expired) . ' جلسة منتهية زمنياً:');
            
            foreach ($expired as $session) {
                $this->line("   • {$session->username} (بدأت: {$session->acctstarttime})");
                
                if (!$dryRun) {
                    $result = $coa->disconnect($session->username);
                    if ($result['success']) {
                        $radius->closeSession($session->acctsessionid);
                        $this->info("     ✅ مفصول");
                    } else {
                        $this->error("     ❌ {$result['message']}");
                    }
                }
            }
        }
        
        $this->newLine();
        
        // ═══════════════════════════════════════
        // 2. تجاوز حد البيانات
        // ═══════════════════════════════════════
        $overQuota = $radius->getOverQuotaUsers();
        
        if (empty($overQuota)) {
            $this->info('✅ لا توجد تجاوزات في البيانات');
        } else {
            $this->warn('📊 ' . count($overQuota) . ' مستخدم تجاوز الحد:');
            
            foreach ($overQuota as $user) {
                $usageMb = round($user->total_usage / 1024 / 1024, 2);
                $this->line("   • {$user->username} (استهلك: {$usageMb} MB)");
                
                if (!$dryRun) {
                    $result = $coa->disconnect($user->username);
                    if ($result['success']) {
                        $this->info("     ✅ مفصول");
                    } else {
                        $this->error("     ❌ {$result['message']}");
                    }
                }
            }
        }
        
        $this->newLine();
        $this->info('✅ الفحص مكتمل');
        
        return 0;
    }
}
