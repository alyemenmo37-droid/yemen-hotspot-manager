<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'color',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة: فئة → مستويات سرعة (hasMany)
     */
    public function speedLevels(): HasMany
    {
        return $this->hasMany(SpeedLevel::class);
    }

    /**
     * عدد البطاقات في الفئة
     */
    public function cardsCount(): int
    {
        return Card::whereHas('speedLevel', function ($q) {
            $q->where('category_id', $this->id);
        })->count();
    }
}
