<?php
declare(strict_types=1);

namespace Yhm\Utils;

class Str
{
    public static function random(int $length = 10): string
    {
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }
        return $digits;
    }
    
    public static function mask(string $input): string
    {
        if (strlen($input) < 4) return $input;
        return substr($input, 0, 3) . '****' . substr($input, -3);
    }
    
    public static function money(float $amount): string
    {
        return number_format($amount, 0) . ' ريال';
    }
    
    public static function bytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
