<?php
/**
 * YHM v0.2 - Main Entry
 */

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

// ═══════════════════════════════════════
// Services
// ═══════════════════════════════════════
$cardService = new CardService($db);
$reportRepo = new ReportRepository($db);

// ═══════════════════════════════════════
// Routing
// ═══════════════════════════════════════
$action = $_GET['action'] ?? 'dashboard';

try {
    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                csrf_check();
                $cardService->add(
                    $_POST['number'],
                    (int)$_POST['amount'],
                    $_POST['speed'],
                    (int)($_POST['data_limit'] ?? 0)
                );
                flash('success', 'تم إضافة البطاقة');
                redirect('?action=dashboard');
            }
            break;
            
        case 'generate':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                csrf_check();
                $count = min((int)$_POST['count'], 100);
                $cards = $cardService->generateBatch(
                    $count,
                    (int)$_POST['amount'],
                    $_POST['speed'],
                    (int)($_POST['data_limit'] ?? 0)
                );
                flash('success', "تم توليد " . count($cards) . " بطاقة");
                redirect('?action=dashboard');
            }
            break;
            
        case 'activate':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                csrf_check();
                if ($cardService->activate($_POST['number'])) {
                    flash('success', 'تم التفعيل');
                }
                redirect('?action=dashboard');
            }
            break;
            
        case 'delete':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                csrf_check();
                if ($cardService->delete($_POST['number'])) {
                    flash('success', 'تم الحذف');
                }
                redirect('?action=dashboard');
            }
            break;
    }
} catch (Exception $e) {
    flash('error', $e->getMessage());
    redirect('?action=dashboard');
}

// ═══════════════════════════════════════
// View
// ═══════════════════════════════════════
require __DIR__ . '/../views/dashboard.php';
