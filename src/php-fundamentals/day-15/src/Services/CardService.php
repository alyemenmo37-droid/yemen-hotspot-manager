<?php
declare(strict_types=1);

class CardService
{
    public function __construct(
        private Database $db,
        private ?RateLimiter $limiter = null,
    ) {}
    
    public function add(string $number, int $amount, string $speed, int $dataLimitMb = 0): Card
    {
        // Validation
        InputValidator::cardNumber($number);
        InputValidator::amount($amount);
        InputValidator::speed($speed);
        
        // Check duplicate
        if ($this->db->fetchColumn("SELECT COUNT(*) FROM cards WHERE number = ?", [$number]) > 0) {
            throw new RuntimeException("البطاقة {$number} موجودة");
        }
        
        $id = $this->db->insert('cards', [
            'number' => $number,
            'amount' => $amount,
            'speed' => $speed,
            'status' => Card::STATUS_UNUSED,
            'data_limit_mb' => $dataLimitMb,
        ]);
        
        $this->log('create', $id, "إضافة بطاقة $number");
        
        return new Card($id, $number, $amount, $speed, Card::STATUS_UNUSED, $dataLimitMb);
    }
    
    public function find(string $number): ?Card
    {
        $data = $this->db->fetchOne("SELECT * FROM cards WHERE number = ?", [$number]);
        return $data ? Card::fromArray($data) : null;
    }
    
    public function activate(string $number): bool
    {
        $card = $this->find($number);
        if ($card === null || !$card->isAvailable()) return false;
        
        $n = $this->db->update('cards', [
            'status' => Card::STATUS_ACTIVE,
            'activated_at' => date('Y-m-d H:i:s'),
        ], 'number = ?', [$number]);
        
        if ($n > 0) $this->log('activate', $card->id, "تفعيل $number");
        return $n > 0;
    }
    
    public function delete(string $number): bool
    {
        return $this->db->delete('cards', 'number = ?', [$number]) > 0;
    }
    
    public function generateBatch(int $count, int $amount, string $speed, int $dataLimit = 0): array
    {
        $cards = [];
        for ($i = 0; $i < $count; $i++) {
            do {
                $number = '';
                for ($j = 0; $j < 10; $j++) $number .= random_int(0, 9);
            } while ($this->find($number) !== null);
            
            $cards[] = $this->add($number, $amount, $speed, $dataLimit);
        }
        $this->log('batch', null, "توليد $count بطاقة");
        return $cards;
    }
    
    public function all(): array
    {
        return array_map(fn($d) => Card::fromArray($d),
            $this->db->fetchAll("SELECT * FROM cards ORDER BY id DESC"));
    }
    
    public function byStatus(string $status): array
    {
        return array_map(fn($d) => Card::fromArray($d),
            $this->db->fetchAll("SELECT * FROM cards WHERE status = ? ORDER BY id DESC", [$status]));
    }
    
    public function stats(): array
    {
        $r = $this->db->fetchOne("
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN status = 'unused' THEN 1 ELSE 0 END) AS unused,
                COALESCE(SUM(CASE WHEN status != 'unused' THEN amount ELSE 0 END), 0) AS sales,
                COALESCE(SUM(data_limit_mb), 0) AS total_data
            FROM cards
        ");
        
        return [
            'total' => (int)$r['total'],
            'active' => (int)$r['active'],
            'unused' => (int)$r['unused'],
            'sales' => (int)$r['sales'],
            'totalData' => (int)$r['total_data'],
        ];
    }
    
    private function log(string $action, ?int $targetId, string $details): void
    {
        $this->db->insert('activity_log', [
            'action' => $action,
            'target_type' => 'card',
            'target_id' => $targetId,
            'details' => $details,
        ]);
    }
}
