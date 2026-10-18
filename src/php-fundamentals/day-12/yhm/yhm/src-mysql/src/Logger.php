<?php
/**
 * YHM - Logger Class
 * تسجيل العمليات
 */

class Logger
{
    public function __construct(
        private string $logFile,
    ) {
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    public function info(string $message): void
    {
        $this->write('INFO', $message);
    }
    
    public function warning(string $message): void
    {
        $this->write('WARN', $message);
    }
    
    public function error(string $message): void
    {
        $this->write('ERROR', $message);
    }
    
    public function success(string $message): void
    {
        $this->write('OK', $message);
    }
    
    public function getRecent(int $count = 10): array
    {
        if (!file_exists($this->logFile)) {
            return [];
        }
        
        $lines = file($this->logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        return array_slice($lines, -$count);
    }
    
    private function write(string $level, string $message): void
    {
        $line = sprintf(
            "[%s] %-5s | %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message
        );
        
        file_put_contents($this->logFile, $line, FILE_APPEND);
    }
}
