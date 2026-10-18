<?php
/**
 * Day 6 - Inheritance
 * الوراثة في OOP
 */

echo "╔" . str_repeat("═", 55) . "╗\n";
echo "║  🧬 الوراثة (Inheritance)                        ║\n";
echo "╚" . str_repeat("═", 55) . "╝\n\n";


// ═══════════════════════════════════════
// 1. Class أساسي (Parent)
// ═══════════════════════════════════════
abstract class Product
{
    public function __construct(
        protected string $name,
        protected int $price,
    ) {}
    
    // method مجردة (يجب على الوارثين تنفيذها)
    abstract public function getDescription(): string;
    
    // method عادية
    public function getPrice(): int
    {
        return $this->price;
    }
    
    public function show(): void
    {
        printf("  • %s - %d ريال\n    %s\n",
            $this->name,
            $this->price,
            $this->getDescription()
        );
    }
}


// ═══════════════════════════════════════
// 2. Class وارث - بطاقة
// ═══════════════════════════════════════
class CardProduct extends Product
{
    public function __construct(
        string $name,
        int $price,
        private string $speed,
        private int $durationMinutes,
    ) {
        parent::__construct($name, $price);
    }
    
    public function getDescription(): string
    {
        $hours = round($this->durationMinutes / 60, 1);
        return "⚡ السرعة: {$this->speed} | ⏱️ المدة: {$hours} ساعة";
    }
    
    public function getSpeed(): string
    {
        return $this->speed;
    }
}


// ═══════════════════════════════════════
// 3. Class وارث - جهاز
// ═══════════════════════════════════════
class DeviceProduct extends Product
{
    public function __construct(
        string $name,
        int $price,
        private string $brand,
        private int $warrantyMonths,
    ) {
        parent::__construct($name, $price);
    }
    
    public function getDescription(): string
    {
        return "🏷️ الماركة: {$this->brand} | 🛡️ الضمان: {$this->warrantyMonths} شهر";
    }
}


// ═══════════════════════════════════════
// 4. الاستخدام
// ═══════════════════════════════════════
echo "📌 المنتجات:\n";

$products = [
    new CardProduct('بطاقة متوسطة', 250, '256k/1M', 1440),
    new CardProduct('بطاقة عالية', 500, '512k/2M', 2880),
    new CardProduct('بطاقة مفتوحة', 1000, '2M/15M', 7200),
    new DeviceProduct('راوتر MikroTik', 15000, 'MikroTik', 24),
    new DeviceProduct('كابل شبكة Cat6', 500, 'Generic', 3),
];

foreach ($products as $p) {
    $p->show();
    echo "\n";
}


// ═══════════════════════════════════════
// 5. Interface
// ═══════════════════════════════════════
interface Exportable
{
    public function toJson(): string;
    public function toCsv(): string;
}

class ExportableCard extends Product implements Exportable
{
    public function __construct(
        string $name,
        int $price,
        private string $speed,
    ) {
        parent::__construct($name, $price);
    }
    
    public function getDescription(): string
    {
        return "السرعة: {$this->speed}";
    }
    
    public function toJson(): string
    {
        return json_encode([
            'name' => $this->name,
            'price' => $this->price,
            'speed' => $this->speed,
        ], JSON_UNESCAPED_UNICODE);
    }
    
    public function toCsv(): string
    {
        return "{$this->name},{$this->price},{$this->speed}";
    }
}

echo "📌 Interface Exportable:\n";

$exportable = new ExportableCard('بطاقة VIP', 2000, '10M/50M');

echo "  JSON: " . $exportable->toJson() . "\n";
echo "  CSV:  " . $exportable->toCsv() . "\n";


// ═══════════════════════════════════════
// 6. Trait
// ═══════════════════════════════════════
trait Timestampable
{
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;
    
    public function setCreated(): void
    {
        $this->createdAt = date('Y-m-d H:i:s');
    }
    
    public function touch(): void
    {
        $this->updatedAt = date('Y-m-d H:i:s');
    }
    
    public function getTimestamps(): array
    {
        return [
            'created' => $this->createdAt,
            'updated' => $this->updatedAt,
        ];
    }
}

class TimestampedCard extends ExportableCard
{
    use Timestampable;
}

echo "\n📌 Trait Timestampable:\n";

$tc = new TimestampedCard('بطاقة موقّتة', 300, '512k/1M');
$tc->setCreated();
sleep(1);
$tc->touch();

$times = $tc->getTimestamps();
echo "  Created: {$times['created']}\n";
echo "  Updated: {$times['updated']}\n";


echo "\n✅ اكتمل التمرين!\n";
