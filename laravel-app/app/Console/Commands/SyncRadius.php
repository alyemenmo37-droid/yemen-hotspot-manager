<?php

namespace App\Console\Commands;

use App\Models\Card;
use App\Services\Mikrotik\MockManager;
use App\Services\Radius\RadiusService;
use Illuminate\Console\Command;

class SyncRadius extends Command
{
    protected $signature = 'radius:sync 
                            {--clear : حذف أولاً}
                            {--only-radius : RADIUS فقط}
                            {--only-mock : Mock فقط}';
    protected $description = 'مزامنة البطاقات مع RADIUS + Mock';

    public function handle(RadiusService $radius, MockManager $mock): int
    {
        $onlyRadius = $this->option('only-radius');
        $onlyMock = $this->option('only-mock');
        
        // ═══════════════════════════════════════
        // 1. تنظيف
        // ═══════════════════════════════════════
        if ($this->option('clear')) {
            $this->warn('🗑️ تنظيف...');
            
            if (!$onlyMock) {
                $count = \DB::table('radcheck')->count();
                \DB::table('radcheck')->delete();
                \DB::table('radreply')->delete();
                \DB::table('radusergroup')->delete();
                $this->info("   RADIUS: حذف $count مستخدم");
            }
            
            if (!$onlyRadius) {
                $count = $mock->clearAll();
                $this->info("   Mock: حذف $count مستخدم");
            }
        }
        
        // ═══════════════════════════════════════
        // 2. مزامنة
        // ═══════════════════════════════════════
        $cards = Card::all();
        $this->info("📇 مزامنة " . $cards->count() . " بطاقة...");
        
        $bar = $this->output->createProgressBar($cards->count());
        $bar->start();
        
        $radiusSuccess = 0;
        $radiusFailed = 0;
        $mockSuccess = 0;
        $mockFailed = 0;
        
        foreach ($cards as $card) {
            // 2.1 RADIUS
            if (!$onlyMock) {
                $result = $radius->addUser($card->number, $card->speed, 1440);
                if ($result['success']) {
                    $radiusSuccess++;
                } else {
                    $radiusFailed++;
                }
            }
            
            // 2.2 Mock
            if (!$onlyRadius) {
                $result = $mock->addUser($card->number, $card->number, [
                    'rate-limit' => $card->speed,
                    'limit-uptime' => '1d',
                ]);
                if ($result['success']) {
                    $mockSuccess++;
                } else {
                    $mockFailed++;
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        // ═══════════════════════════════════════
        // 3. النتائج
        // ═══════════════════════════════════════
        if (!$onlyMock) {
            $this->info("✅ RADIUS: $radiusSuccess نجح");
            if ($radiusFailed > 0) {
                $this->error("❌ RADIUS: $radiusFailed فشل");
            }
        }
        
        if (!$onlyRadius) {
            $this->info("✅ Mock: $mockSuccess نجح");
            if ($mockFailed > 0) {
                $this->error("❌ Mock: $mockFailed فشل");
            }
        }
        
        $this->newLine();
        $this->info("📊 الإجمالي:");
        if (!$onlyMock) {
            $this->line("   RADIUS: " . \DB::table('radcheck')->count() . " مستخدم");
        }
        if (!$onlyRadius) {
            $this->line("   Mock: " . $mock->getUsersCount() . " مستخدم");
        }
        
        return 0;
    }
}
