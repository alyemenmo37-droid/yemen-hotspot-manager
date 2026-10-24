<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    
    // ═══════════════════════════════════════
    // الثوابت
    // ═══════════════════════════════════════
    public const STATUS_UNUSED = 'unused';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_STOPPED = 'stopped';
    
    // ═══════════════════════════════════════
    // Mass Assignment Protection
    // الحقول المسموح بتعبئتها دفعة واحدة
    // ═══════════════════════════════════════
    protected $fillable = [
        'number',
        'amount',
        'speed',
        'status',
        'data_limit_mb',
        'data_used_mb',
        'activated_at',
        'expires_at',
    ];
    
    // ═══════════════════════════════════════
    // Type Casting
    // تحويل الأنواع تلقائياً
    // ═══════════════════════════════════════
    protected $casts = [
        'amount' => 'integer',
        'data_limit_mb' => 'integer',
        'data_used_mb' => 'integer',
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    // ═══════════════════════════════════════
    // Accessors & Methods
    // ═══════════════════════════════════════
    
    /**
     * هل البطاقة نشطة؟
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * هل البطاقة متاحة؟
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_UNUSED;
    }
    
    /**
     * نسبة استهلاك البيانات
     */
    public function dataPercentage(): float
    {
        if (empty($this->data_limit_mb) || $this->data_limit_mb === 0) {
            return 0.0;
        }
        return round($this->data_used_mb / $this->data_limit_mb * 100, 1);
    }
    
    /**
     * تفعيل البطاقة
     */
    public function activate(): void
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'activated_at' => now(),
        ]);
    }
    
    /**
     * إيقاف البطاقة
     */
    public function stop(): void
    {
        $this->update(['status' => self::STATUS_STOPPED]);
    }
    
    /**
     * إنهاء البطاقة
     */
    public function expire(): void
    {
        $this->update(['status' => self::STATUS_EXPIRED]);
    }
    
    /**
     * تسمية مختصرة
     */
    public function label(): string
    {
        $emoji = match ($this->status) {
            self::STATUS_ACTIVE => '✅',
            self::STATUS_UNUSED => '⏸️',
            self::STATUS_EXPIRED => '⌛',
            self::STATUS_STOPPED => '🛑',
            default => '❓',
        };
        
        return "$emoji {$this->number} | {$this->amount} ريال | {$this->speed}";
    }
    
    /**
     * إخفاء جزء من الرقم
     */
    public function maskedNumber(): string
    {
        return substr($this->number, 0, 3) . '****' . substr($this->number, -3);
    }
    
    // ═══════════════════════════════════════
    // Scopes (استعلامات جاهزة)
    // ═══════════════════════════════════════
    
    /**
     * بطاقات نشطة فقط
     * 
     * الاستخدام: Card::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    
    /**
     * بطاقات متاحة فقط
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_UNUSED);
    }
    
    /**
     * بحث برقم البطاقة
     */
    public function scopeByNumber($query, string $number)
    {
        return $query->where('number', 'like', "%$number%");
    }
}
/**
 * العلاقة: بطاقة → مستوى سرعة (belongsTo)
 */
public function speedLevel(): BelongsTo
{
    return $this->belongsTo(SpeedLevel::class);
}
