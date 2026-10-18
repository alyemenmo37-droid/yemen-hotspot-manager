<?php
/**
 * YHM - Card Model
 * نموذج البطاقة
 */

class Card
{
    // الحالات الممكنة
    public const STATUS_UNUSED = 'unused';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_STOPPED = 'stopped';
    
    public function __construct(
        public readonly string $number,
        public readonly int $amount,
        public readonly string $speed,
        public string $status = self::STATUS_UNUSED,
        public ?string $createdAt = null,
        public ?string $activatedAt = null,
    ) {
        if ($this->createdAt === null) {
            $this->createdAt = date('Y-m-d H:i:s');
        }
    }
    
    /**
     * إنشاء من مصفوفة
     */
    public static function fromArray(array $data): self
    {
        return new self(
            number: $data['number'],
            amount: $data['amount'],
            speed: $data['speed'],
            status: $data['status'] ?? self::STATUS_UNUSED,
            createdAt: $data['createdAt'] ?? null,
            activatedAt: $data['activatedAt'] ?? null,
        );
    }
    
    /**
     * تحويل إلى مصفوفة
     */
    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'amount' => $this->amount,
            'speed' => $this->speed,
            'status' => $this->status,
            'createdAt' => $this->createdAt,
            'activatedAt' => $this->activatedAt,
        ];
    }
    
    /**
     * تفعيل
     */
    public function activate(): void
    {
        $this->status = self::STATUS_ACTIVE;
        $this->activatedAt = date('Y-m-d H:i:s');
    }
    
    /**
     * إنهاء
     */
    public function expire(): void
    {
        $this->status = self::STATUS_EXPIRED;
    }
    
    /**
     * إيقاف
     */
    public function stop(): void
    {
        $this->status = self::STATUS_STOPPED;
    }
    
    /**
     * هل نشطة؟
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * هل متاحة؟
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_UNUSED;
    }
    
    /**
     * وصف مختصر
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
        
        return sprintf(
            "%s %s | %d ريال | %s | %s",
            $emoji,
            $this->number,
            $this->amount,
            $this->speed,
            $this->status,
        );
    }
    
    /**
     * إخفاء جزء من الرقم
     */
    public function masked(): string
    {
        return substr($this->number, 0, 3) . '****' . substr($this->number, -3);
    }
}
