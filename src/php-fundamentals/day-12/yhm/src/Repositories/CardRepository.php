<?php
/**
 * YHM - CardRepository
 * الوصول للبطاقات مع العلاقات
 */

declare(strict_types=1);

class CardRepository
{
    public function __construct(
        private Database $db,
    ) {}
    
    /**
     * كل البطاقات مع تفاصيل العلاقات
     */
    public function allWithRelations(): array
    {
        return $this->db->fetchAll("
            SELECT 
                c.id,
                c.number,
                c.amount,
                c.speed,
                c.status,
                c.data_limit_mb,
                c.data_used_mb,
                c.created_at,
                c.activated_at,
                s.name AS speed_name,
                s.rate_limit,
                cat.name AS category_name,
                COALESCE(
                    (SELECT GROUP_CONCAT(g.name SEPARATOR ', ')
                     FROM card_group_pivot p
                     INNER JOIN card_groups g ON g.id = p.group_id
                     WHERE p.card_id = c.id),
                    ''
                ) AS groups_list
            FROM cards c
            LEFT JOIN speed_levels s ON s.id = c.speed_level_id
            LEFT JOIN categories cat ON cat.id = s.category_id
            ORDER BY c.id DESC
        ");
    }
    
    /**
     * بطاقة واحدة بالتفاصيل
     */
    public function findWithRelations(string $number): ?array
    {
        $card = $this->db->fetchOne("
            SELECT 
                c.*,
                s.name AS speed_name,
                s.rate_limit,
                s.upload_speed,
                s.download_speed,
                cat.name AS category_name
            FROM cards c
            LEFT JOIN speed_levels s ON s.id = c.speed_level_id
            LEFT JOIN categories cat ON cat.id = s.category_id
            WHERE c.number = ?
        ", [$number]);
        
        if ($card === null) {
            return null;
        }
        
        // جلب المجموعات
        $card['groups'] = $this->db->fetchAll("
            SELECT g.id, g.name
            FROM card_groups g
            INNER JOIN card_group_pivot p ON p.group_id = g.id
            WHERE p.card_id = ?
        ", [$card['id']]);
        
        return $card;
    }
    
    /**
     * حسب الفئة
     */
    public function byCategory(): array
    {
        return $this->db->fetchAll("
            SELECT 
                cat.id,
                cat.name AS category_name,
                COUNT(c.id) AS cards_count,
                SUM(c.amount) AS total_value,
                SUM(CASE WHEN c.status = 'active' THEN 1 ELSE 0 END) AS active_count
            FROM categories cat
            LEFT JOIN speed_levels s ON s.category_id = cat.id
            LEFT JOIN cards c ON c.speed_level_id = s.id
            GROUP BY cat.id, cat.name
            ORDER BY cat.id
        ");
    }
    
    /**
     * حسب المجموعة
     */
    public function byGroup(int $groupId): array
    {
        return $this->db->fetchAll("
            SELECT 
                c.id,
                c.number,
                c.amount,
                c.status,
                c.data_limit_mb,
                c.data_used_mb
            FROM cards c
            INNER JOIN card_group_pivot p ON p.card_id = c.id
            WHERE p.group_id = ?
            ORDER BY c.id DESC
        ", [$groupId]);
    }
    
    /**
     * إحصائيات شاملة
     */
    public function comprehensiveStats(): array
    {
        return [
            'general' => $this->db->fetchOne("
                SELECT 
                    COUNT(*) AS total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
                    SUM(CASE WHEN status = 'unused' THEN 1 ELSE 0 END) AS unused,
                    COALESCE(SUM(CASE WHEN status != 'unused' THEN amount ELSE 0 END), 0) AS sales
                FROM cards
            "),
            'byCategory' => $this->byCategory(),
            'dataUsage' => $this->db->fetchOne("
                SELECT 
                    SUM(data_limit_mb) AS total_limit,
                    SUM(data_used_mb) AS total_used,
                    ROUND(AVG(data_used_mb / NULLIF(data_limit_mb, 0) * 100), 2) AS avg_percentage
                FROM cards
                WHERE data_limit_mb > 0
            "),
        ];
    }
}
