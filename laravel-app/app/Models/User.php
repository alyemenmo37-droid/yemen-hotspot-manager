<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * الحقول المسموح بتعبئتها
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * الحقول المخفية
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type Casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',  // Laravel 10+ يُشفّر تلقائياً
        'is_active' => 'boolean',
    ];

    /**
     * هل المستخدم مسؤول؟
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * هل الحساب نشط؟
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * الاسم المختصر
     */
    public function initials(): string
    {
        $parts = explode(' ', $this->name);

        return strtoupper(substr($parts[0], 0, 1).(isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }
}
