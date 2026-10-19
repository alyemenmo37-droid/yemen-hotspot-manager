<?php
/**
 * YHM v0.2 - Bootstrap
 * تحميل كل الفئات
 */

declare(strict_types=1);

// ═══════════════════════════════════════
// 1. الإعدادات
// ═══════════════════════════════════════
$config = require __DIR__ . '/config/database.php';

// ═══════════════════════════════════════
// 2. Autoloader
// ═══════════════════════════════════════
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/src/Core/',
        __DIR__ . '/src/Models/',
        __DIR__ . '/src/Repositories/',
        __DIR__ . '/src/Services/',
        __DIR__ . '/src/Security/',
        __DIR__ . '/src/Controllers/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ═══════════════════════════════════════
// 3. Database
// ═══════════════════════════════════════
$db = new Database($config);

// ═══════════════════════════════════════
// 4. جلسة
// ═══════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ═══════════════════════════════════════
// 5. مساعدات
// ═══════════════════════════════════════
function e(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function flash(string $key, ?string $value = null): ?string {
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }
    
    $result = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $result;
}

function csrf(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_check(): void {
    $token = $_POST['_token'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('CSRF token mismatch');
    }
}
