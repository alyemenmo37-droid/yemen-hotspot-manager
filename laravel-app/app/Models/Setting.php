<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];
    
    /**
     * جلب إعداد
     * 
     * ⚠️ مهم: نخزن القيمة فقط، وليس Model
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // خزّن القيمة المحسوبة (int/bool/string) — وليس Model
        return Cache::remember("setting:{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return match ($setting->type) {
                'int' => (int) $setting->value,
                'bool' => (bool) $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }
    
    /**
     * حفظ إعداد
     */
    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): void
    {
        $value = match ($type) {
            'bool' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };
        
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'group' => $group]
        );
        
        Cache::forget("setting:{$key}");
    }
}
