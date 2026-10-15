<?php
/**
 * Day 6 - OOP Basics
 * YHM Project
 */

echo "╔" . str_repeat("═", 55) . "╗\n";
echo "║  🎯 أساسيات OOP في PHP                              ║\n";
echo "╚" . str_repeat("═", 55) . "╝\n\n";


// ═══════════════════════════════════════
// 1. أبسط Class
// ═══════════════════════════════════════
class Card
{
    // Properties
    public string $number = '';
    public int $amount = 0;
    public string $status = 'unused';
    
    // Method
    public function show(): void
    {
        echo "البطاقة: {$this->number} | {$this->amount} ريال | {$this->status}\n";
    }
    
    public function activate(): void
    {
        $this->status = 'active';
        echo "✅ تم تفعيل {$this->number}\n";
    }
}


// ═══════════════════════════════════════
// 2. إنشاء كائنات
// ═══════════════════════════════════════
echo "📌 إنشاء كائنات:\n";

$card1 = new Card();
$card1->number = '1234567890';
$card1->amount = 250;
$card1->show();

$card2 = new Card();
$card2->number = '0987654321';
$card2->amount = 500;
$card2->show();

$card1->activate();
$card1->show();


// ═══════════════════════════════════════
// 3. Constructor
// ═══════════════════════════════════════
class BetterCard
{
    public string $number;
    public int $amount;
    public string $status;
    
    public function __construct(string $number, int $amount, string $status = 'unused')
    {
        $this->number = $number;
        $this->amount = $amount;
        $this->status = $status;
    }
    
    public function show(): void
    {
        printf("  💳 %s | %d ريال | %s\n", 
            $this->number, $this->amount, $this->status);
    }
}

echo "\n📌 Constructor (اختصار):\n";

$c1 = new BetterCard('1111111111', 100);
$c2 = new BetterCard('2222222222', 250, 'active');
$c3 = new BetterCard('3333333333', 500, 'expired');

$c1->show();
$c2->show();
$c3->show();


// ═══════════════════════════════════════
// 4. Property Promotion (PHP 8+)
// ═══════════════════════════════════════
class FastCard
{
    public function __construct(
        public string $number,
        public int $amount,
        public string $status = 'unused',
    ) {}
    
    public function show(): void
    {
        echo "  ⚡ {$this->number} | {$this->amount} | {$this->status}\n";
    }
}

echo "\n📌 Property Promotion (PHP 8+):\n";

$fc = new FastCard('4444444444', 1000, 'active');
$fc->show();


// ═══════════════════════════════════════
// 5. Public vs Private
// ═══════════════════════════════════════
class SafeCard
{
    private string $number;
    private int $amount;
    private bool $activated = false;
    
    public function __construct(string $number, int $amount)
    {
        if (!preg_match('/^\d{10}$/', $number)) {
            throw new InvalidArgumentException('رقم البطاقة غير صالح');
        }
        
        $this->number = $number;
        $this->amount = $amount;
    }
    
    // Getter
    public function getNumber(): string
    {
        return substr($this->number, 0, 3) . '****' . substr($this->number, -3);
    }
    
    public function getAmount(): int
    {
        return $this->amount;
    }
    
    // Method للتفعيل
    public function activate(): void
    {
        $this->activated = true;
    }
    
    public function isActivated(): bool
    {
        return $this->activated;
    }
    
    public function show(): void
    {
        $status = $this->activated ? '✅ نشط' : '⏸️ غير نشط';
        echo "  🔒 {$this->getNumber()} | {$this->amount} | $status\n";
    }
}

echo "\n📌 Public vs Private:\n";

$sc = new SafeCard('5555555555', 250);
$sc->show();
$sc->activate();
$sc->show();

// $sc->number = 'xyz';  // ❌ خطأ! private


// ═══════════════════════════════════════
// 6. Static Methods
// ═══════════════════════════════════════
class CardFactory
{
    public static int $totalCreated = 0;
    
    public static function generate(int $length = 10): string
    {
        self::$totalCreated++;
        
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= random_int(0, 9);
        }
        return $number;
    }
    
    public static function getTotal(): int
    {
        return self::$totalCreated;
    }
}

echo "\n📌 Static Methods:\n";
echo "  بطاقة 1: " . CardFactory::generate() . "\n";
echo "  بطاقة 2: " . CardFactory::generate() . "\n";
echo "  بطاقة 3: " . CardFactory::generate() . "\n";
echo "  الإجمالي المولّد: " . CardFactory::getTotal() . "\n";


echo "\n✅ اكتمل التمرين!\n";
