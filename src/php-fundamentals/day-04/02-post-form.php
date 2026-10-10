<?php
/**
 * Day 4 - POST Form + CSRF
 */

session_start();

// CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$validCards = [
    '1234567890' => ['balance' => 250, 'speed' => '256k/1M', 'active' => true],
    '0987654321' => ['balance' => 500, 'speed' => '512k/2M', 'active' => true],
];

$error = '';
$success = '';
$user = $_SESSION['user'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'رمز الحماية غير صالح';
    } else {
        $card = trim($_POST['card'] ?? '');
        
        if (!preg_match('/^\d{10}$/', $card)) {
            $error = 'رقم البطاقة يجب أن يكون 10 أرقام';
        } elseif (!isset($validCards[$card])) {
            $error = 'البطاقة غير موجودة';
        } else {
            $_SESSION['user'] = [
                'card' => $card,
                'balance' => $validCards[$card]['balance'],
                'speed' => $validCards[$card]['speed'],
                'login_at' => date('Y-m-d H:i:s'),
            ];
            $user = $_SESSION['user'];
            $success = 'تم تسجيل الدخول بنجاح';
        }
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>YHM - Hotspot Login</title>
    <style>
        body { font-family: Arial; background: linear-gradient(135deg, #009639, #007a2e); min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; padding: 20px; }
        .container { max-width: 450px; width: 100%; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .logo { text-align: center; font-size: 48px; }
        h1 { color: #009639; text-align: center; margin: 10px 0; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; }
        input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 16px; text-align: center; }
        input:focus { outline: none; border-color: #009639; }
        button { background: #009639; color: white; padding: 15px; border: none; border-radius: 8px; cursor: pointer; margin-top: 20px; width: 100%; font-size: 16px; font-weight: bold; }
        button:hover { background: #007a2e; }
        .alert { padding: 12px; border-radius: 8px; margin: 15px 0; }
        .alert-error { background: #ffebee; color: #c62828; }
        .alert-success { background: #e8f5e9; color: #2e7d32; }
        .info { background: #f5f5f5; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .logout { background: #c62828; padding: 10px; text-align: center; border-radius: 8px; margin-top: 15px; display: block; text-decoration: none; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">📡</div>
        <h1>Yemen Hotspot Manager</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (!$user): ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <label>رقم البطاقة:</label>
                <input type="text" name="card" pattern="\d{10}" maxlength="10" placeholder="1234567890" required autofocus>
                <button type="submit">دخول</button>
            </form>
            <p style="text-align:center; color:#999; font-size:12px; margin-top:15px;">
                🧪 للتجربة: 1234567890 | 0987654321
            </p>
        <?php else: ?>
            <div class="info">
                <h3>👤 مرحباً بك</h3>
                <p><strong>البطاقة:</strong> <?= htmlspecialchars($user['card']) ?></p>
                <p><strong>الرصيد:</strong> <?= $user['balance'] ?> ريال</p>
                <p><strong>السرعة:</strong> <?= htmlspecialchars($user['speed']) ?></p>
                <p><strong>وقت الدخول:</strong> <?= $user['login_at'] ?></p>
            </div>
            <a href="?logout=1" class="logout">خروج</a>
        <?php endif; ?>
    </div>
</body>
</html>
