<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpeedLevel extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'rate_limit',
        'price',
        'duration_minutes',
        'data_limit_mb',
        'is_default',
        'is_active',
    ];
    
    protected $casts = [
        'price' => 'integer',
        'duration_minutes' => 'integer',
        'data_limit_mb' => 'integer',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    /**
     * العلاقة: مستوى سرعة → فئة (belongsTo)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * العلاقة: مستوى سرعة → بطاقات (hasMany)
     */
    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
    
    /**
     * تنسيق المدة
     */
    public function durationFormatted(): string
    {
        $minutes = $this->duration_minutes;
        
        if ($minutes < 60) return "$minutes دقيقة";
        if ($minutes < 1440) return round($minutes / 60) . " ساعة";
        if ($minutes < 10080) return round($minutes / 1440) . " يوم";
        return round($minutes / 10080) . " أسبوع";
    }
}
