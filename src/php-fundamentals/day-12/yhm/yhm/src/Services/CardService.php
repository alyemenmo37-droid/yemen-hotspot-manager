<?php
/**
 * YHM - CardService (MySQL)
 */

declare(strict_types=1);

class CardService
{
    public function __construct(
        private Database $db,
    ) {}
    
    /**
     * إضافة بطاقة
     */
    public function add(string $number, int $amount, string $speed): Card
    {
        if (!preg_match('/^\d{10}$/', $number)) {
            throw new InvalidArgumentException('رقم البطاقة يجب أن يكون 10 أرقام');
        }
        
        // تحقق من التكرار
        $exists = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM cards WHERE number = ?",
            [$number]
        );
        
        if ($exists > 0) {
            throw new RuntimeException("البطاقة {$number} موجودة مسبقاً");
        }
        
        // إدراج
        $id = $this->db->insert('cards', [
            'number' => $number,
            'amount' => $amount,
            'speed' => $speed,
            'status' => Card::STATUS_UNUSED,
        ]);
        
        // تسجيل
        $this->log('create', $id, "إضافة بطاقة $number");
        
        return new Card(
            id: $id,
            number: $number,
            amount: $amount,
            speed: $speed,
        );
    }
    
    /**
     * البحث
     */
    public function find(string $number): ?Card
    {
        $data = $this->db->fetchOne(
            "SELECT * FROM cards WHERE number = ?",
            [$number]
        );
        
        return $data ? Card::fromArray($data) : null;
    }
    
    /**
     * تفعيل
     */
    public function activate(string $number): bool
    {
        $card = $this->find($number);
        if ($card === null || !$card->isAvailable()) {
            return false;
        }
        
        $updated = $this->db->update(
            'cards',
            [
                'status' => Card::STATUS_ACTIVE,
                'activated_at' => date('Y-m-d H:i:s'),
            ],
            'number = ?',
            [$number]
        );
        
        if ($updated > 0) {
            $this->log('activate', $card->id, "تفعيل بطاقة $number");
        }
        
        return $updated > 0;
    }
    
    /**
     * حذف
     */
    public function delete(string $number): bool
    {
        $updated = $this->db->delete('cards', 'number = ?', [$number]);
        return $updated > 0;
    }
    
    /**
     * توليد دفعة
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
        
        $this->log('batch', null, "توليد $count بطاقة");
        
        return $cards;
    }
    
    /**
     * كل البطاقات
     */
    public function all(): array
    {
        $data = $this->db->fetchAll("SELECT * FROM cards ORDER BY id DESC");
        return array_map(fn($d) => Card::fromArray($d), $data);
    }
    
    /**
     * حسب الحالة
     */
    public function byStatus(string $status): array
    {
        $data = $this->db->fetchAll(
            "SELECT * FROM cards WHERE status = ? ORDER BY id DESC",
            [$status]
        );
        return array_map(fn($d) => Card::fromArray($d), $data);
    }
    
    /**
     * إحصائيات
     */
    public function stats(): array
    {
        $row = $this->db->fetchOne("
            SELECT 
                COUNT(*) AS total,
                COALESCE(SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END), 0) AS active,
                COALESCE(SUM(CASE WHEN status = 'unused' THEN 1 ELSE 0 END), 0) AS unused,
                COALESCE(SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END), 0) AS expired,
                COALESCE(SUM(CASE WHEN status != 'unused' THEN amount ELSE 0 END), 0) AS sales,
                COALESCE(SUM(amount), 0) AS total_value
            FROM cards
        ");
        
        return [
            'total' => (int) $row['total'],
            'byStatus' => [
                'active' => (int) $row['active'],
                'unused' => (int) $row['unused'],
                'expired' => (int) $row['expired'],
            ],
            'sales' => (int) $row['sales'],
            'totalValue' => (int) $row['total_value'],
        ];
    }
    
    /**
     * تسجيل
     */
    private function log(string $action, ?int $targetId, string $details): void
    {
        $this->db->insert('activity_log', [
            'action' => $action,
            'target_type' => 'card',
            'target_id' => $targetId,
            'details' => $details,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
