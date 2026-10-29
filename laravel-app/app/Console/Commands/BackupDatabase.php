<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database 
                            {--keep=7 : عدد النسخ المحتفظ بها}
                            {--compress : ضغط الملف}';
    protected $description = 'نسخ احتياطي لقاعدة البيانات';

    public function handle(): int
    {
        $this->info('💾 بدء النسخ الاحتياطي...');
        
        // ═══════════════════════════════════════
        // 1. إعداد المسار
        // ═══════════════════════════════════════
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "yhm_backup_{$timestamp}.sql";
        $filepath = "{$backupDir}/{$filename}";
        
        // ═══════════════════════════════════════
        // 2. بناء أمر mysqldump
        // ═══════════════════════════════════════
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s -p%s --single-transaction --quick --lock-tables=false %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg((string) $port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filepath)
        );
        
        // ═══════════════════════════════════════
        // 3. تنفيذ
        // ═══════════════════════════════════════
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->error('❌ فشل النسخ: ' . implode("\n", $output));
            return 1;
        }
        
        // ═══════════════════════════════════════
        // 4. ضغط (اختياري)
        // ═══════════════════════════════════════
        if ($this->option('compress')) {
            $this->line('📦 ضغط الملف...');
            exec("gzip " . escapeshellarg($filepath));
            $filepath .= '.gz';
        }
        
        $size = round(filesize($filepath) / 1024 / 1024, 2);
        $this->info("✅ تم النسخ: " . basename($filepath));
        $this->line("   الحجم: {$size} MB");
        $this->line("   المسار: {$filepath}");
        
        // ═══════════════════════════════════════
        // 5. حذف النسخ القديمة
        // ═══════════════════════════════════════
        $keep = (int) $this->option('keep');
        $files = collect(File::files($backupDir))
            ->sortByDesc(fn($f) => $f->getMTime())
            ->values();
        
        if ($files->count() > $keep) {
            $toDelete = $files->slice($keep);
            foreach ($toDelete as $file) {
                File::delete($file->getPathname());
            }
            $this->line("🗑️ حذف " . $toDelete->count() . " نسخة قديمة");
        }
        
        return 0;
    }
}
