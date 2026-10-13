<?php
/**
 * YHM v0.1 - الواجهة الرئيسية
 * اليوم 7 - المشروع الأسبوعي
 */

declare(strict_types=1);

require_once __DIR__ . '/../src/Logger.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Models/Card.php';
require_once __DIR__ . '/../src/Services/CardService.php';

// الإعداد
$dataDir = __DIR__ . '/../data';
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);

$service = new CardService(
    new Database($dataDir . '/cards.json'),
    new Logger($dataDir . '/yhm.log')
);

// ═══════════════════════════════════════
// معالجة الإجراءات
// ═══════════════════════════════════════
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            // إضافة بطاقة يدوية
            case 'add':
                $number = trim($_POST['number'] ?? '');
                $amount = (int)($_POST['amount'] ?? 0);
                $speed  = trim($_POST['speed'] ?? '');
                
                $service->add($number, $amount, $speed);
                $message = "✅ تم إضافة البطاقة $number";
                $messageType = 'success';
                break;
            
            // توليد دفعة
            case 'generate':
                $count  = min((int)($_POST['count'] ?? 10), 100);
                $amount = (int)($_POST['amount'] ?? 250);
                $speed  = trim($_POST['speed'] ?? '256k/1M');
                
                $cards = $service->generateBatch($count, $amount, $speed);
                $message = "🎲 تم توليد " . count($cards) . " بطاقة";
                $messageType = 'success';
                break;
            
            // تفعيل
            case 'activate':
                $number = $_POST['number'] ?? '';
                if ($service->activate($number)) {
                    $message = "✅ تم تفعيل $number";
                    $messageType = 'success';
                }
                break;
            
            // حذف
            case 'delete':
                $number = $_POST['number'] ?? '';
                if ($service->delete($number)) {
                    $message = "🗑️ تم حذف $number";
                    $messageType = 'success';
                }
                break;
        }
    } catch (Exception $e) {
        $message = "❌ " . $e->getMessage();
        $messageType = 'error';
    }
}

// ═══════════════════════════════════════
// جلب البيانات
// ═══════════════════════════════════════
$filter = $_GET['filter'] ?? 'all';
$search = trim($_GET['search'] ?? '');

$allCards = match ($filter) {
    'active'   => $service->byStatus(Card::STATUS_ACTIVE),
    'unused'   => $service->byStatus(Card::STATUS_UNUSED),
    'expired'  => $service->byStatus(Card::STATUS_EXPIRED),
    default    => $service->all(),
};

// فلترة البحث
if ($search !== '') {
    $allCards = array_filter($allCards, function ($card) use ($search) {
        return str_contains($card->number, $search);
    });
}

$stats = $service->stats();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YHM v0.1 - لوحة الإدارة</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">📡</div>
        <h1>Yemen Hotspot Manager</h1>
        <p class="version">v0.1 — لوحة الإدارة</p>
    </header>
    
    <main>
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <!-- الإحصائيات -->
        <section class="stats">
            <div class="stat">
                <div class="stat-value"><?= $stats['total'] ?></div>
                <div class="stat-label">إجمالي البطاقات</div>
            </div>
            <div class="stat">
                <div class="stat-value"><?= $stats['byStatus']['active'] ?? 0 ?></div>
                <div class="stat-label">نشطة</div>
            </div>
            <div class="stat">
                <div class="stat-value"><?= $stats['byStatus']['unused'] ?? 0 ?></div>
                <div class="stat-label">غير مستخدمة</div>
            </div>
            <div class="stat">
                <div class="stat-value"><?= number_format($stats['totalSales']) ?></div>
                <div class="stat-label">المبيعات (ريال)</div>
            </div>
        </section>
        
        <!-- نماذج الإدخال -->
        <section class="forms">
            <div class="card-form">
                <h3>➕ إضافة بطاقة</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="number" pattern="\d{10}" placeholder="رقم البطاقة (10 أرقام)" required>
                    <input type="number" name="amount" placeholder="السعر" value="250" required>
                    <select name="speed" required>
                        <option value="512k/512k">بطيء - 512k/512k</option>
                        <option value="256k/1M" selected>متوسطة - 256k/1M</option>
                        <option value="512k/2M">عالية - 512k/2M</option>
                        <option value="2M/15M">مفتوح - 2M/15M</option>
                    </select>
                    <button type="submit">إضافة</button>
                </form>
            </div>
            
            <div class="card-form">
                <h3>🎲 توليد دفعة</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="generate">
                    <input type="number" name="count" placeholder="العدد" value="10" min="1" max="100" required>
                    <input type="number" name="amount" placeholder="السعر" value="250" required>
                    <select name="speed" required>
                        <option value="512k/512k">بطيء</option>
                        <option value="256k/1M" selected>متوسطة</option>
                        <option value="512k/2M">عالية</option>
                        <option value="2M/15M">مفتوح</option>
                    </select>
                    <button type="submit">توليد</button>
                </form>
            </div>
        </section>
        
        <!-- البحث والفلترة -->
        <section class="filters">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="🔍 بحث برقم البطاقة" value="<?= htmlspecialchars($search) ?>">
                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                <button type="submit">بحث</button>
            </form>
            
            <div class="filter-buttons">
                <a href="?filter=all" class="<?= $filter === 'all' ? 'active' : '' ?>">الكل</a>
                <a href="?filter=active" class="<?= $filter === 'active' ? 'active' : '' ?>">نشطة</a>
                <a href="?filter=unused" class="<?= $filter === 'unused' ? 'active' : '' ?>">غير مستخدمة</a>
                <a href="?filter=expired" class="<?= $filter === 'expired' ? 'active' : '' ?>">منتهية</a>
            </div>
        </section>
        
        <!-- جدول البطاقات -->
        <section class="cards-table">
            <h3>📋 البطاقات (<?= count($allCards) ?>)</h3>
            
            <?php if (empty($allCards)): ?>
                <p class="empty">لا توجد بطاقات للعرض</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>السعر</th>
                            <th>السرعة</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($allCards as $card): ?>
                            <tr>
                                <td class="number"><?= htmlspecialchars($card->number) ?></td>
                                <td><?= $card->amount ?> ريال</td>
                                <td><span class="speed"><?= htmlspecialchars($card->speed) ?></span></td>
                                <td>
                                    <span class="status status-<?= $card->status ?>">
                                        <?php
                                        echo match($card->status) {
                                            Card::STATUS_ACTIVE => '✅ نشطة',
                                            Card::STATUS_UNUSED => '⏸️ غير مستخدمة',
                                            Card::STATUS_EXPIRED => '⌛ منتهية',
                                            Card::STATUS_STOPPED => '🛑 موقوفة',
                                            default => $card->status,
                                        };
                                        ?>
                                    </span>
                                </td>
                                <td class="date"><?= $card->createdAt ?></td>
                                <td class="actions">
                                    <?php if ($card->isAvailable()): ?>
                                        <form method="POST" style="display:inline">
                                            <input type="hidden" name="action" value="activate">
                                            <input type="hidden" name="number" value="<?= htmlspecialchars($card->number) ?>">
                                            <button type="submit" class="btn-activate" title="تفعيل">⚡</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" style="display:inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="number" value="<?= htmlspecialchars($card->number) ?>">
                                        <button type="submit" class="btn-delete" title="حذف">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
    
    <footer>
        <p>YHM v0.1 © 2026 — Made with ❤️ in Yemen 🇾🇪</p>
    </footer>
</body>
</html>
