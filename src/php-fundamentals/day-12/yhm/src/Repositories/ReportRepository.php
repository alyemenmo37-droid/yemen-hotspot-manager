<?php
/**
 * YHM - ReportRepository
 * تقارير احترافية
 */

declare(strict_types=1);

class ReportRepository
{
    public function __construct(
        private Database $db,
    ) {}
    
    /**
     * تقرير المبيعات حسب الفئة
     */
    public function salesByCategory(): array
    {
        return $this->db->fetchAll("
            SELECT 
                cat.name AS category,
                COUNT(c.id) AS cards_count,
                SUM(c.amount) AS revenue,
                SUM(CASE WHEN c.status = 'active' THEN 1 ELSE 0 END) AS active
            FROM categories cat
            LEFT JOIN speed_levels s ON s.category_id = cat.id
            LEFT JOIN cards c ON c.speed_level_id = s.id
            WHERE c.status != 'unused'
            GROUP BY cat.id, cat.name
            ORDER BY revenue DESC
        ");
    }
    
    /**
     * تصنيف الأسعار
     */
    public function priceRanges(): array
    {
        return $this->db->fetchAll("
            SELECT 
                CASE 
                    WHEN amount <= 100 THEN 'رخيصة'
                    WHEN amount <= 250 THEN 'متوسطة'
                    WHEN amount <= 500 THEN 'مرتفعة'
                    WHEN amount <= 1000 THEN 'عالية'
                    ELSE 'مميزة'
                END AS price_range,
                COUNT(*) AS count,
                SUM(amount) AS total
            FROM cards
            GROUP BY price_range
            ORDER BY total DESC
        ");
    }
    
    /**
     * أفضل البطاقات
     */
    public function topCards(int $limit = 5): array
    {
        return $this->db->fetchAll("
            SELECT 
                number,
                amount,
                status,
                ROW_NUMBER() OVER (ORDER BY amount DESC) AS rank_num
            FROM cards
            ORDER BY amount DESC
            LIMIT ?
        ", [$limit]);
    }
    
    /**
     * استخدام البيانات
     */
    public function dataUsage(): array
    {
        return $this->db->fetchOne("
            SELECT 
                COALESCE(SUM(data_limit_mb), 0) AS total_limit,
                COALESCE(SUM(data_used_mb), 0) AS total_used,
                COUNT(CASE WHEN data_used_mb >= data_limit_mb THEN 1 END) AS exceeded_count
            FROM cards
            WHERE data_limit_mb > 0
        ");
    }
    
    /**
     * النشاط اليومي
     */
    public function dailyActivity(int $days = 7): array
    {
        return $this->db->fetchAll("
            SELECT 
                DATE(created_at) AS day,
                COUNT(*) AS cards_created,
                SUM(CASE WHEN status != 'unused' THEN amount ELSE 0 END) AS revenue
            FROM cards
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY day DESC
        ", [$days]);
    }
}
