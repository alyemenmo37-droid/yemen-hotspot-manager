<?php
/**
 * YHM - Card Model
 * Namespace: Yhm\Models
 */

declare(strict_types=1);

namespace Yhm\Models;

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
        public readonly int $dataLimitMb = 0,
        public readonly int $dataUsedMb = 0,
    ) {}
    
    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? (int)$data['id'] : null,
            number: $data['number'],
            amount: (int)$data['amount'],
            speed: $data['speed'],
            status: $data['status'] ?? self::STATUS_UNUSED,
            dataLimitMb: (int)($data['data_limit_mb'] ?? 0),
            dataUsedMb: (int)($data['data_used_mb'] ?? 0),
        );
    }
    
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    public function dataPercentage(): float
    {
        if ($this->dataLimitMb === 0) return 0.0;
        return round($this->dataUsedMb / $this->dataLimitMb * 100, 1);
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
        
        $data = $this->dataLimitMb > 0
            ? sprintf(" | 📊 %d/%d MB", $this->dataUsedMb, $this->dataLimitMb)
            : '';
        
        return "$emoji {$this->number} | {$this->amount} ريال | {$this->speed}{$data}";
    }
}
