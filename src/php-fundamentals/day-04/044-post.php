<?php
/**
 * Day 4+ - Hotspot Login with Time Management
 */

session_start();

// ====== إدارة ملف البطاقات ======
define('CARDS_FILE', __DIR__ . '/cards.json');

function loadCards() {
    if (!file_exists(CARDS_FILE)) {
        $default = [
            '1234567890' => [
                'balance' => 250, 'speed' => '256k/1M',
                'total_minutes' => 10, 'remaining_seconds' => 600, 'active' => true,
            ],
            '0987654321' => [
                'balance' => 500, 'speed' => '512k/2M',
                'total_minutes' => 20, 'remaining_seconds' => 1200, 'active' => true,
            ],
        ];
        file_put_contents(CARDS_FILE, json_encode($default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $default;
    }
    return json_decode(file_get_contents(CARDS_FILE), true) ?: [];
}

function saveCards($cards) {
    file_put_contents(CARDS_FILE, json_encode($cards, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function getRemainingSeconds($card, $cards) {
    if (!isset($cards[$card])) return 0;
    $c = $cards[$card];
    if (!empty($c['in_use']) && !empty($c['started_at'])) {
        $elapsed = time() - $c['started_at'];
        return max(0, $c['remaining_seconds'] - $elapsed);
    }
    return $c['remaining_seconds'];
}

function formatTime($seconds) {
    return sprintf('%02d:%02d', floor($seconds / 60), $seconds % 60);
}

// ====== CSRF ======
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';
$user = $_SESSION['user'] ?? null;

// ====== تسجيل الخروج ======
if (isset($_GET['logout'])) {
    if (!empty($_SESSION['user']['card'])) {
        $card = $_SESSION['user']['card'];
        $cards = loadCards();
        if (isset($cards[$card])) {
            $remaining = getRemainingSeconds($card, $cards);
            $cards[$card]['remaining_seconds'] = $remaining;
            $cards[$card]['in_use'] = false;
            unset($cards[$card]['started_at']);
            if ($remaining <= 0) {
                $cards[$card]['active'] = false;
            }
            saveCards($cards);
        }
    }
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// ====== تسجيل الدخول ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = 'رمز الحماية غير صالح';
    } else {
        $card = trim($_POST['card'] ?? '');
        $cards = loadCards();

        if (!preg_match('/^\d{10}$/', $card)) {
            $error = 'رقم البطاقة يجب أن يكون 10 أرقام';
        } elseif (!isset($cards[$card])) {
            $error = 'البطاقة غير موجودة';
        } elseif (!$cards[$card]['active']) {
            $error = 'هذه البطاقة منتهية أو ملغاة';
        } else {
            $remaining = getRemainingSeconds($card, $cards);
            if ($remaining <= 0) {
                $cards[$card]['active'] = false;
                $cards[$card]['remaining_seconds'] = 0;
                $cards[$card]['in_use'] = false;
                saveCards($cards);
                $error = 'انتهى وقت هذه البطاقة';
            } else {
                $cards[$card]['in_use'] = true;
                $cards[$card]['started_at'] = time();
                $cards[$card]['remaining_seconds'] = $remaining;
                saveCards($cards);

                $_SESSION['user'] = [
                    'card' => $card,
                    'balance' => $cards[$card]['balance'],
                    'speed' => $cards[$card]['speed'],
                    'login_at' => date('Y-m-d H:i:s'),
                ];
                $user = $_SESSION['user'];
                $success = 'تم تسجيل الدخول بنجاح';
            }
        }
    }
}

// حساب الوقت للعرض
$remainingSeconds = 0;
$isActive = false;
if ($user) {
    $cards = loadCards();
    $remainingSeconds = getRemainingSeconds($user['card'], $cards);
    $isActive = $cards[$user['card']]['active'] ?? false;

    // إذا انتهى الوقت أثناء الجلسة
    if ($remainingSeconds <= 0 && $isActive) {
        $cards[$user['card']]['active'] = false;
        $cards[$user['card']]['remaining_seconds'] = 0;
        $cards[$user['card']]['in_use'] = false;
        saveCards($cards);
        $isActive = false;
    }
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
        #timer { font-size: 24px; font-weight: bold; color: #009639; font-family: monospace; }
        .timer-danger { color: #c62828 !important; }
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
                <p><strong>الحالة:</strong> <?= $isActive ? '🟢 نشطة' : '🔴 منتهية' ?></p>
                <hr>
                <p><strong>⏱️ الوقت المتبقي:</strong><br>
                    <span id="timer" data-seconds="<?= $remainingSeconds ?>">
                        <?= formatTime($remainingSeconds) ?>
                    </span>
                </p>
            </div>
            <a href="?logout=1" class="logout">خروج</a>

            <script>
                const timerEl = document.getElementById('timer');
                if (timerEl) {
                    let seconds = parseInt(timerEl.dataset.seconds);
                    const interval = setInterval(() => {
                        seconds--;
                        if (seconds <= 0) {
                            clearInterval(interval);
                            timerEl.textContent = '00:00';
                            alert('انتهى وقت البطاقة! سيتم تسجيل الخروج.');
                            window.location.href = '?logout=1';
                        } else {
                            const m = String(Math.floor(seconds / 60)).padStart(2, '0');
                            const s = String(seconds % 60).padStart(2, '0');
                            timerEl.textContent = `${m}:${s}`;
                            if (seconds < 60) timerEl.classList.add('timer-danger');
                        }
                    }, 1000);
                }
            </script>
        <?php endif; ?>
    </div>
</body>
</html>
