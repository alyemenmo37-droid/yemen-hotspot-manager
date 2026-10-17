<?php
/**
 * Day 12 - Dashboard
 */

declare(strict_types=1);

require_once __DIR__ . '/../day-10/bootstrap.php';

$pdo = getPDO();

// جمع الإحصائيات
$stats = $pdo->query("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active,
        SUM(CASE WHEN status = 'unused' THEN 1 ELSE 0 END) AS unused,
        COALESCE(SUM(CASE WHEN status != 'unused' THEN amount ELSE 0 END), 0) AS sales,
        COALESCE(AVG(amount), 0) AS avg_price,
        COALESCE(SUM(data_limit_mb), 0) AS total_data,
        COALESCE(SUM(data_used_mb), 0) AS used_data
    FROM cards
")->fetch();

$topSpeed = $pdo->query("
    SELECT s.name, COUNT(c.id) AS count
    FROM speed_levels s
    LEFT JOIN cards c ON c.speed_level_id = s.id
    GROUP BY s.id, s.name
    ORDER BY count DESC
    LIMIT 1
")->fetch();

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>YHM Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f0f0f0; padding: 20px; }
        .dashboard { max-width: 1200px; margin: 0 auto; }
        h1 { color: #009639; margin-bottom: 20px; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card-value { font-size: 36px; font-weight: bold; color: #009639; }
        .card-label { color: #666; margin-top: 5px; font-size: 14px; }
        .card-value.danger { color: #c62828; }
        .card-value.warning { color: #f9a825; }
        .progress {
            background: #eee;
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
            margin-top: 10px;
        }
        .progress-bar {
            height: 100%;
            background: #009639;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>📡 YHM Dashboard</h1>
        
        <div class="grid">
            <div class="card">
                <div class="card-value"><?= $stats['total'] ?></div>
                <div class="card-label">إجمالي البطاقات</div>
            </div>
            
            <div class="card">
                <div class="card-value"><?= $stats['active'] ?></div>
                <div class="card-label">نشطة</div>
            </div>
            
            <div class="card">
                <div class="card-value warning"><?= $stats['unused'] ?></div>
                <div class="card-label">غير مستخدمة</div>
            </div>
            
            <div class="card">
                <div class="card-value"><?= number_format((float)$stats['sales']) ?></div>
                <div class="card-label">المبيعات (ريال)</div>
            </div>
            
            <div class="card">
                <div class="card-value"><?= number_format((float)$stats['avg_price'], 0) ?></div>
                <div class="card-label">متوسط السعر</div>
            </div>
            
            <div class="card">
                <div class="card-value"><?= $topSpeed ? $topSpeed['name'] : '-' ?></div>
                <div class="card-label">السرعة الأكثر مبيعاً (<?= $topSpeed ? $topSpeed['count'] : 0 ?>)</div>
            </div>
        </div>
        
        <div class="card" style="margin-bottom: 20px;">
            <h3>📊 استهلاك البيانات</h3>
            <?php
            $totalGB = $stats['total_data'] / 1024;
            $usedGB = $stats['used_data'] / 1024;
            $percent = $stats['total_data'] > 0 
                ? round($stats['used_data'] / $stats['total_data'] * 100, 1) 
                : 0;
            ?>
            <p style="margin: 15px 0;">
                <strong><?= round($usedGB, 2) ?> GB</strong>
                من <strong><?= round($totalGB, 2) ?> GB</strong>
                (<?= $percent ?>%)
            </p>
            <div class="progress">
                <div class="progress-bar" style="width: <?= $percent ?>%"></div>
            </div>
        </div>
    </div>
</body>
</html>
