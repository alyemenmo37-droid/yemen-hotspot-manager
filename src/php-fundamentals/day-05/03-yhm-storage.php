<?php
/**
 * YHM Project - Card Storage System
 * اليوم 5 - نظام تخزين بالملفات
 */

declare(strict_types=1);

// ═══════════════════════════════════════
// 1. إعدادات
// ═══════════════════════════════════════
$DATA_FILE = __DIR__ . '/yhm-cards.json';
$LOG_FILE  = __DIR__ . '/yhm-log.txt';

// ═══════════════════════════════════════
// 2. الفئة الرئيسية
// ═══════════════════════════════════════
class CardStorage
{
    private string $dataFile;
    private string $logFile;
    
    public function __construct(string $dataFile, string $logFile)
    {
        $this->dataFile = $dataFile;
        $this->logFile = $logFile;
        
        // إنشاء الملف إذا لم يكن موجوداً
        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, '[]');
        }
    }
    
    /**
     * قراءة كل البطاقات
     */
    public function all(): array
    {
        $content = file_get_contents($this->dataFile);
        $cards = json_decode($content, true) ?: [];
        return $cards;
    }
    
    /**
     * حفظ كل البطاقات
     */
    private function save(array $cards): void
    {
        file_put_contents(
            $this->dataFile,
            json_encode($cards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }
    
    /**
     * تسجيل عملية
     */
    private function log(string $action, string $details): void
    {
        $line = sprintf(
            "[%s] %-10s | %s\n",
            date('Y-m-d H:i:s'),
            $action,
            $details
        );
        file_put_contents($this->logFile, $line, FILE_APPEND);
    }
    
    /**
     * إضافة بطاقة جديدة
     */
    public function add(string $number, int $amount, string $speed): bool
    {
        $cards = $this->all();
        
        // التحقق من عدم التكرار
        foreach ($cards as $c) {
            if ($c['number'] === $number) {
                $this->log('ERROR', "تكرار: $number");
                return false;
            }
        }
        
        $cards[] = [
            'number' => $number,
            'amount' => $amount,
            'speed' => $speed,
            'status' => 'unused',
            'created' => date('Y-m-d H:i:s'),
        ];
        
        $this->save($cards);
        $this->log('ADD', "$number | $amount | $speed");
        return true;
    }
    
    /**
     * البحث عن بطاقة
     */
    public function find(string $number): ?array
    {
        foreach ($this->all() as $c) {
            if ($c['number'] === $number) {
                return $c;
            }
        }
        return null;
    }
    
    /**
     * حذف بطاقة
     */
    public function delete(string $number): bool
    {
        $cards = $this->all();
        $initial = count($cards);
        
        $cards = array_values(array_filter(
            $cards,
            fn($c) => $c['number'] !== $number
        ));
        
        if (count($cards) < $initial) {
            $this->save($cards);
            $this->log('DELETE', $number);
            return true;
        }
        
        return false;
    }
    
    /**
     * تغيير حالة البطاقة
     */
    public function setStatus(string $number, string $status): bool
    {
        $cards = $this->all();
        $changed = false;
        
        foreach ($cards as &$c) {
            if ($c['number'] === $number) {
                $c['status'] = $status;
                $changed = true;
                break;
            }
        }
        unset($c);
        
        if ($changed) {
            $this->save($cards);
            $this->log('UPDATE', "$number → $status");
        }
        
        return $changed;
    }
    
    /**
     * إحصائيات
     */
    public function stats(): array
    {
        $cards = $this->all();
        $byStatus = [];
        $totalSales = 0;
        
        foreach ($cards as $c) {
            $byStatus[$c['status']] = ($byStatus[$c['status']] ?? 0) + 1;
            if ($c['status'] !== 'unused') {
                $totalSales += $c['amount'];
            }
        }
        
        return [
            'total' => count($cards),
            'byStatus' => $byStatus,
            'totalSales' => $totalSales,
        ];
    }
    
    /**
     * توليد بطاقة عشوائية
     */
    public function generate(int $amount, string $speed, int $count = 1): array
    {
        $generated = [];
        
        for ($i = 0; $i < $count; $i++) {
            $number = '';
            for ($j = 0; $j < 10; $j++) {
                $number .= random_int(0, 9);
            }
            
            if ($this->add($number, $amount, $speed)) {
                $generated[] = $number;
            }
        }
        
        return $generated;
    }
}


// ═══════════════════════════════════════
// 3. الاختبار
// ═══════════════════════════════════════

echo "╔" . str_repeat("═", 60) . "╗\n";
echo "║  📡 YHM - نظام تخزين البطاقات (JSON)                     ║\n";
echo "╚" . str_repeat("═", 60) . "╝\n\n";

$storage = new CardStorage($DATA_FILE, $LOG_FILE);

// 3.1 ابدأ نظيفاً
if (file_exists($DATA_FILE)) {
    unlink($DATA_FILE);
    unlink($LOG_FILE);
}
$storage = new CardStorage($DATA_FILE, $LOG_FILE);

// 3.2 إضافة بطاقات يدوياً
echo "📝 إضافة 3 بطاقات:\n";
$storage->add('1234567890', 250, '256k/1M');
$storage->add('0987654321', 500, '512k/2M');
$storage->add('1122334455', 1000, '2M/15M');
echo "   ✅ تم إضافة 3 بطاقات\n";

// 3.3 توليد بطاقات عشوائية
echo "\n🎲 توليد 5 بطاقات عشوائية:\n";
$generated = $storage->generate(250, '256k/1M', 5);
foreach ($generated as $num) {
    echo "   • $num\n";
}

// 3.4 البحث
echo "\n🔍 البحث عن بطاقة:\n";
$found = $storage->find('1234567890');
if ($found) {
    echo "   ✅ وُجدت: {$found['number']} | {$found['amount']} ريال\n";
}

// 3.5 تغيير الحالة
echo "\n🔄 تفعيل بطاقة:\n";
$storage->setStatus('1234567890', 'active');
echo "   ✅ تم التفعيل\n";

$storage->setStatus('0987654321', 'expired');
echo "   ✅ تم الإنهاء\n";

// 3.6 الإحصائيات
$stats = $storage->stats();
echo "\n📊 الإحصائيات:\n";
echo "   إجمالي: {$stats['total']}\n";
foreach ($stats['byStatus'] as $status => $count) {
    echo "   • $status: $count\n";
}
echo "   المبيعات: {$stats['totalSales']} ريال\n";

// 3.7 حذف
echo "\n🗑️ حذف بطاقة:\n";
$storage->delete('1122334455');
echo "   ✅ تم الحذف\n";

// 3.8 عرض الملف النهائي
echo "\n📄 محتوى yhm-cards.json:\n";
echo str_repeat("─", 60) . "\n";
$content = file_get_contents($DATA_FILE);
$data = json_decode($content, true);
foreach ($data as $c) {
    printf("  %s | %5d | %-10s | %s\n",
        $c['number'], $c['amount'], $c['speed'], $c['status']);
}
echo str_repeat("─", 60) . "\n";

// 3.9 السجل
echo "\n📋 السجل (آخر 5 عمليات):\n";
$logLines = file($LOG_FILE, FILE_IGNORE_NEW_LINES);
foreach (array_slice($logLines, -5) as $line) {
    echo "   $line\n";
}

echo "\n🎉 اكتمل المشروع — YHM اليوم 5 مكتمل!\n";
