<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MikrotikLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'action',
        'card_number',
        'status',
        'message',
        'mode',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIconAttribute(): string
    {
        return match ($this->action) {
            'add_user' => '➕',
            'remove_user' => '🗑️',
            'disconnect' => '🔌',
            'change_speed' => '⚡',
            default => '📝',
        };
    }
}
