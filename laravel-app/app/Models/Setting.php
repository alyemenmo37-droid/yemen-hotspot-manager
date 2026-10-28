<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];
    
    /**
     * جلب إعداد
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $cached = Cache::remember("setting:{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });
        
        if (!$cached) {
            return $default;
        }
        
        return match ($cached->type) {
            'int' => (int) $cached->value,
            'bool' => (bool) $cached->value,
            'json' => json_decode($cached->value, true),
            default => $cached->value,
        };
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
