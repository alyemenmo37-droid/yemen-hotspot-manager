<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'speed_level_id',
        'status',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function speedLevel(): BelongsTo
    {
        return $this->belongsTo(SpeedLevel::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' 
            && $this->ends_at->isFuture();
    }
}
