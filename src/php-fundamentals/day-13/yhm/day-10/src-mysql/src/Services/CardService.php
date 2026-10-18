<?php
/**
 * YHM - CardService
 * منطق إدارة البطاقات
 */

class CardService
{
    public function __construct(
        private Database $db,
        private Logger $logger,
    ) {}
    
    /**
     * إضافة بطاقة جديدة
     */
    public function add(string $number, int $amount, string $speed): Card
    {
        // تحقق من الصيغة
        if (!preg_match('/^\d{10}$/', $number)) {
            throw new InvalidArgumentException('رقم البطاقة يجب أن يكون 10 أرقام');
        }
        
        // تحقق من التكرار
        if ($this->db->findBy('number', $number) !== null) {
            throw new RuntimeException("البطاقة {$number} موجودة مسبقاً");
        }
        
        // إنشاء البطاقة
        $card = new Card($number, $amount, $speed);
        
        // حفظ
        $this->db->insert($card->toArray());
        $this->logger->info("ADD | {$number} | {$amount} | {$speed}");
        
        return $card;
    }
    
    /**
     * البحث عن بطاقة
     */
    public function find(string $number): ?Card
    {
        $data = $this->db->findBy('number', $number);
        
        return $data ? Card::fromArray($data) : null;
    }
    
    /**
     * تفعيل بطاقة
     */
    public function activate(string $number): bool
    {
        $card = $this->find($number);
        
        if ($card === null) {
            return false;
        }
        
        $card->activate();
        $this->db->update('number', $number, $card->toArray());
        $this->logger->success("ACTIVATE | {$number}");
        
        return true;
    }
    
    /**
     * حذف بطاقة
     */
    public function delete(string $number): bool
    {
        $deleted = $this->db->delete('number', $number);
        
        if ($deleted) {
            $this->logger->warning("DELETE | {$number}");
        }
        
        return $deleted;
    }
    
    /**
     * توليد دفعة عشوائية
     */
    public function generateBatch(int $count, int $amount, string $speed): array
    {
        $cards = [];
        
        for ($i = 0; $i < $count; $i++) {
            do {
                $number = '';
                for ($j = 0; $j < 10; $j++) {
                    $number .= random_int(0, 9);
                }
            } while ($this->find($number) !== null);
            
            $cards[] = $this->add($number, $amount, $speed);
        }
        
        $this->logger->success("BATCH | Generated {$count} cards");
        
        return $cards;
    }
    
    /**
     * كل البطاقات
     */
    public function all(): array
    {
        return array_map(
            fn($data) => Card::fromArray($data),
            $this->db->all()
        );
    }
    
    /**
     * فلترة حسب الحالة
     */
    public function byStatus(string $status): array
    {
        $data = $this->db->filter(
            fn($row) => ($row['status'] ?? '') === $status
        );
        
        return array_map(fn($d) => Card::fromArray($d), $data);
    }
    
    /**
     * إحصائيات
     */
    public function stats(): array
    {
        $all = $this->all();
        $byStatus = [];
        $totalSales = 0;
        $totalValue = 0;
        
        foreach ($all as $card) {
            $byStatus[$card->status] = ($byStatus[$card->status] ?? 0) + 1;
            $totalValue += $card->amount;
            
            if ($card->status !== Card::STATUS_UNUSED) {
                $totalSales += $card->amount;
            }
        }
        
        return [
            'total' => count($all),
            'byStatus' => $byStatus,
            'totalSales' => $totalSales,
            'totalValue' => $totalValue,
        ];
    }
}
