<?php
/**
 * Card Model مع data_limit
 */

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
            dataLimitMb: (int)($data['data_limit_mb'] ?? 0),
            dataUsedMb: (int)($data['data_used_mb'] ?? 0),
            createdAt: $data['created_at'] ?? null,
            activatedAt: $data['activated_at'] ?? null,
        );
    }
    
    /**
     * هل تجاوز الحد؟
     */
    public function hasExceededData(): bool
    {
        return $this->dataLimitMb > 0 && $this->dataUsedMb >= $this->dataLimitMb;
    }
    
    /**
     * المتبقي (MB)
     */
    public function dataRemaining(): int
    {
        if ($this->dataLimitMb === 0) return PHP_INT_MAX;
        return max(0, $this->dataLimitMb - $this->dataUsedMb);
    }
    
    /**
     * النسبة المئوية للاستهلاك
     */
    public function dataPercentage(): float
    {
        if ($this->dataLimitMb === 0) return 0.0;
        return round($this->dataUsedMb / $this->dataLimitMb * 100, 1);
    }
    
    /**
     * عرض البطاقة
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
        
        $data = $this->dataLimitMb > 0
            ? sprintf(" | 📊 %d/%d MB (%.1f%%)", 
                $this->dataUsedMb, 
                $this->dataLimitMb,
                $this->dataPercentage()
            )
            : '';
        
        return sprintf(
            "%s %s | %d ريال | %s | %s%s",
            $emoji,
            $this->number,
            $this->amount,
            $this->speed,
            $this->status,
            $data,
        );
    }
    
    /**
     * تحويل لوحدات مقروءة
     */
    public function dataLimitHuman(): string
    {
        return $this->humanSize($this->dataLimitMb * 1024 * 1024);
    }
    
    public function dataUsedHuman(): string
    {
        return $this->humanSize($this->dataUsedMb * 1024 * 1024);
    }
    
    private function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
