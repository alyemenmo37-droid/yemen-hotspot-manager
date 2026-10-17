<?php
/**
 * YHM - Card Model (MySQL)
 */

declare(strict_types=1);

class Card
{
    public const STATUS_UNUSED = 'unused';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_STOPPED = 'stopped';
    
    public function __construct(
        public readonly ?int $id,
        public readonly string $number,
        public readonly int $amount,
        public readonly string $speed,
        public readonly string $status = self::STATUS_UNUSED,
        public readonly ?string $createdAt = null,
        public readonly ?string $activatedAt = null,
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int)$data['id'] : null,
            number: $data['number'],
            amount: (int)$data['amount'],
            speed: $data['speed'],
            status: $data['status'] ?? self::STATUS_UNUSED,
            createdAt: $data['created_at'] ?? null,
            activatedAt: $data['activated_at'] ?? null,
        );
    }
    
    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'amount' => $this->amount,
            'speed' => $this->speed,
            'status' => $this->status,
            'activated_at' => $this->activatedAt,
        ];
    }
    
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_UNUSED;
    }
    
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
}
