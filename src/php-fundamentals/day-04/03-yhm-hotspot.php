<?php
/**
 * YHM - Hotspot Portal (Complete)
 * اليوم 4 - المشروع الشامل
 */

session_start();

// ═══════════════════════════════════════
// 1. بيانات تجريبية (ستكون في DB لاحقاً)
// ═══════════════════════════════════════
$CARDS = [
    '1234567890' => ['balance' => 250,  'speed' => '256k/1M', 'speed_id' => 2],
    '0987654321' => ['balance' => 500,  'speed' => '512k/2M', 'speed_id' => 3],
    '1122334455' => ['balance' => 1000, 'speed' => '2M/15M',  'speed_id' => 4],
];

$SPEEDS = [
    1 => ['name' => 'بطيء',   'value' => '512k/512k', 'price' => 100],
    2 => ['name' => 'متوسطة', 'value' => '256k/1M',   'price' => 250],
    3 => ['name' => 'عالية',  'value' => '512k/2M',   'price' => 500],
    4 => ['name' => 'مفتوح',  'value' => '2M/15M',    'price' => 1000],
];

// ═══════════════════════════════════════
// 2. CSRF
// ═══════════════════════════════════════
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

// ═══════════════════════════════════════
// 3. تسجيل الخروج
// ═══════════════════════════════════════
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: ?');
    exit;
}

// ═══════════════════════════════════════
// 4. معالجة الطلبات
// ═══════════════════════════════════════
$message = '';
$messageType = '';
$page = $_GET['page'] ?? 'login';

// 4.1 تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $message = 'خطأ أمني';
        $messageType = 'error';
    } else {
        $card = trim($_POST['card'] ?? '');
        
        if (!preg_match('/^\d{10}$/', $card)) {
            $message = 'رقم البطاقة غير صالح';
            $messageType = 'error';
        } elseif (!isset($CARDS[$card])) {
            $message = 'البطاقة غير موجودة';
            $messageType = 'error';
        } else {
            $_SESSION['card'] = $card;
            $_SESSION['user'] = $CARDS[$card];
            $message = 'تم تسجيل الدخول';
            $messageType = 'success';
            $page = 'dashboard';
        }
    }
}

// 4.2 تغيير السرعة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_speed') {
    if (isset($_SESSION['card'], $_POST['speed_id'])) {
        $speedId = (int) $_POST['speed_id'];
        if (isset($SPEEDS[$speedId])) {
            $_SESSION['user']['speed'] = $SPEEDS[$speedId]['value'];
            $_SESSION['user']['speed_id'] = $speedId;
            $message = 'تم تغيير السرعة إلى: ' . $SPEEDS[$speedId]['name'];
            $messageType = 'success';
            $page = 'dashboard';
        }
    }
}

// التحقق من تسجيل الدخول
$loggedIn = isset($_SESSION['card']);

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YHM - Hotspot Portal</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: linear-gradient(135deg, #009639 0%, #005a1f 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; }
        .container { max-width: 480px; width: 100%; background: white; padding: 35px; border-radius: 20px; box-shadow: 0 25px 70px rgba(0,0,0,0.35); }
        .logo { text-align: center; font-size: 56px; margin-bottom: 5px; }
        h1 { color: #009639; text-align: center; margin: 0 0 5px; font-size: 24px; }
        .subtitle { text-align: center; color: #888; margin-bottom: 25px; font-size: 13px; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; color: #333; font-size: 14px; }
        input[type="text"] { width: 100%; padding: 14px; border: 2px solid #ddd; border-radius: 10px; box-sizing: border-box; font-size: 18px; text-align: center; letter-spacing: 3px; font-family: monospace; }
        input:focus { outline: none; border-color: #009639; box-shadow: 0 0 0 3px rgba(0,150,57,0.1); }
        button { background: #009639; color: white; padding: 14px; border: none; border-radius: 10px; cursor: pointer; margin-top: 15px; width: 100%; font-size: 16px; font-weight: bold; transition: all 0.2s; }
        button:hover { background: #007a2e; transform: translateY(-1px); box-shadow: 0 5px 15px rgba(0,150,57,0.3); }
        .alert { padding: 12px 15px; border-radius: 10px; margin: 15px 0; font-size: 14px; }
        .alert-error { background: #ffebee; color: #c62828; border-right: 4px solid #c62828; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border-right: 4px solid #2e7d32; }
        .card-info { background: linear-gradient(135deg, #009639, #007a2e); color: white; padding: 25px; border-radius: 15px; margin: 20px 0; }
        .card-info h3 { margin: 0 0 15px; font-size: 18px; opacity: 0.9; }
        .card-info .amount { font-size: 32px; font-weight: bold; margin: 10px 0; }
        .card-info .label { opacity: 0.8; font-size: 12px; }
        .speed-list { margin: 20px 0; }
        .speed-item { display: block; padding: 15px; border: 2px solid #eee; border-radius: 10px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; }
        .speed-item:hover { border-color: #009639; background: #f9fff9; }
        .speed-item input { margin-left: 10px; }
        .speed-item.active { border-color: #009639; background: #e8f5e9; }
        .speed-name { font-weight: bold; color: #333; }
        .speed-value { color: #666; font-size: 13px; margin-right: 10px; }
        .speed-price { float: left; color: #009639; font-weight: bold; }
        .logout { background: #c62828; padding: 12px; text-align: center; border-radius: 10px; margin-top: 15px; display: block; text-decoration: none; color: white; font-weight: bold; }
        .test-hint { text-align: center; color: #bbb; font-size: 11px; margin-top: 20px; }
        .footer { text-align: center; color: #999; font-size: 11px; margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">📡</div>
        <h1>Yemen Hotspot Manager</h1>
        <p class="subtitle">YHM Portal v0.1</p>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $messageType ?>">
                <?= $messageType === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!$loggedIn): ?>
            <!-- ═══════════════════════════════════════ -->
            <!-- صفحة تسجيل الدخول -->
            <!-- ═══════════════════════════════════════ -->
            <form method="POST">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <input type="hidden" name="action" value="login">
                
                <label>🎫 رقم البطاقة:</label>
                <input type="text" name="card" pattern="\d{10}" maxlength="10" placeholder="1234567890" required autofocus>
                
                <button type="submit">🚀 دخول</button>
            </form>
            
            <p class="test-hint">🧪 للتجربة: 1234567890 | 0987654321 | 1122334455</p>
            
        <?php elseif ($page === 'speed'): ?>
            <!-- ═══════════════════════════════════════ -->
            <!-- صفحة اختيار السرعة -->
            <!-- ═══════════════════════════════════════ -->
            <h3 style="text-align: center; color: #333;">⚡ اختر السرعة المناسبة</h3>
            
            <form method="POST">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <input type="hidden" name="action" value="change_speed">
                
                <div class="speed-list">
                    <?php foreach ($SPEEDS as $id => $s): 
                        $isCurrent = ($_SESSION['user']['speed_id'] ?? 0) === $id;
                    ?>
                        <label class="speed-item <?= $isCurrent ? 'active' : '' ?>">
                            <input type="radio" name="speed_id" value="<?= $id ?>" <?= $isCurrent ? 'checked' : '' ?>>
                            <span class="speed-name"><?= $s['name'] ?></span>
                            <span class="speed-value"><?= $s['value'] ?></span>
                            <span class="speed-price"><?= $s['price'] ?> ريال</span>
                        </label>
                    <?php endforeach; ?>
                </div>
                
                <button type="submit">✓ تأكيد الاختيار</button>
                <a href="?" style="display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none;">↩ رجوع</a>
            </form>
            
        <?php else: ?>
            <!-- ═══════════════════════════════════════ -->
            <!-- صفحة Dashboard -->
            <!-- ═══════════════════════════════════════ -->
            <div class="card-info">
                <h3>👤 مرحباً بك</h3>
                <div class="label">البطاقة</div>
                <div style="font-family: monospace; font-size: 18px; letter-spacing: 2px; margin-bottom: 15px;">
                    <?= htmlspecialchars($_SESSION['card']) ?>
                </div>
                <div class="label">الرصيد</div>
                <div class="amount"><?= $_SESSION['user']['balance'] ?> <span style="font-size: 16px;">ريال</span></div>
                <div class="label">السرعة الحالية</div>
                <div style="font-size: 20px; font-weight: bold; margin-top: 5px;">
                    ⚡ <?= htmlspecialchars($_SESSION['user']['speed']) ?>
                </div>
            </div>
            
            <a href="?page=speed" style="display: block; background: #009639; color: white; padding: 15px; text-align: center; border-radius: 10px; text-decoration: none; font-weight: bold; margin-bottom: 10px;">
                ⚡ تغيير السرعة
            </a>
            
            <a href="?action=logout" class="logout">🚪 خروج</a>
        <?php endif; ?>
        
        <div class="footer">
            YHM v0.1 © 2026 — Made in Yemen 🇾🇪
        </div>
    </div>
</body>
</html>
