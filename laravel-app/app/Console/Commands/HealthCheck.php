<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthCheck extends Command
{
    protected $signature = 'health:check';
    protected $description = 'فحص صحة النظام';

    public function handle(): int
    {
        $this->info('🏥 Health Check');
        $this->newLine();
        
        $checks = [
            'Database' => fn() => $this->checkDatabase(),
            'Cache' => fn() => $this->checkCache(),
            'Storage' => fn() => $this->checkStorage(),
            'Routes' => fn() => $this->checkRoutes(),
            'Queue' => fn() => $this->checkQueue(),
            'Migrations' => fn() => $this->checkMigrations(),
        ];
        
        $failed = 0;
        
        foreach ($checks as $name => $check) {
            try {
                $result = $check();
                
                if ($result['status'] === 'ok') {
                    $this->line("  ✅ {$name}: {$result['message']}");
                } elseif ($result['status'] === 'warning') {
                    $this->line("  ⚠️  {$name}: {$result['message']}");
                } else {
                    $this->line("  ❌ {$name}: {$result['message']}");
                    $failed++;
                }
            } catch (\Throwable $e) {
                $this->line("  ❌ {$name}: {$e->getMessage()}");
                $failed++;
            }
        }
        
        $this->newLine();
        
        if ($failed > 0) {
            $this->error("❌ فشل {$failed} فحص");
            return 1;
        }
        
        $this->info('✅ كل الفحوصات نجحت!');
        return 0;
    }
    
    private function checkDatabase(): array
    {
        DB::select('SELECT 1');
        $count = DB::table('cards')->count();
        return ['status' => 'ok', 'message' => "متصل ({$count} بطاقة)"];
    }
    
    private function checkCache(): array
    {
        Cache::put('health_check', 'test', 10);
        $value = Cache::get('health_check');
        return $value === 'test' 
            ? ['status' => 'ok', 'message' => 'يعمل']
            : ['status' => 'error', 'message' => 'فشل'];
    }
    
    private function checkStorage(): array
    {
        $path = storage_path('logs');
        if (!is_writable($path)) {
            return ['status' => 'error', 'message' => 'logs غير قابل للكتابة'];
        }
        return ['status' => 'ok', 'message' => 'قابل للكتابة'];
    }
    
    private function checkRoutes(): array
    {
        $routes = \Route::getRoutes();
        return ['status' => 'ok', 'message' => count($routes) . ' route'];
    }
    
    private function checkQueue(): array
    {
        $failed = DB::table('failed_jobs')->count();
        if ($failed > 10) {
            return ['status' => 'warning', 'message' => "{$failed} job فاشل"];
        }
        return ['status' => 'ok', 'message' => 'لا مشاكل'];
    }
    
    private function checkMigrations(): array
    {
        $pending = DB::table('migrations')->count();
        return ['status' => 'ok', 'message' => "{$pending} migration"];
    }
}
