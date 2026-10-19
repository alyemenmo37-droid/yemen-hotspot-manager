<?php
$stats = $cardService->stats();
$cards = $cardService->all();
$reports = $reportRepo;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YHM v0.2</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>📡 Yemen Hotspot Manager</h1>
        <p>v0.2 — نسخة MySQL</p>
    </header>
    
    <main>
        <?php if ($msg = flash('success')): ?>
            <div class="alert success">✅ <?= e($msg) ?></div>
        <?php endif; ?>
        <?php if ($msg = flash('error')): ?>
            <div class="alert error">❌ <?= e($msg) ?></div>
        <?php endif; ?>
        
        <!-- Stats -->
        <div class="stats">
            <div class="stat">
                <div class="value"><?= $stats['total'] ?></div>
                <div class="label">إجمالي</div>
            </div>
            <div class="stat">
                <div class="value green"><?= $stats['active'] ?></div>
                <div class="label">نشطة</div>
            </div>
            <div class="stat">
                <div class="value orange"><?= $stats['unused'] ?></div>
                <div class="label">غير مستخدمة</div>
            </div>
            <div class="stat">
                <div class="value"><?= number_format($stats['sales']) ?></div>
                <div class="label">المبيعات (ريال)</div>
            </div>
        </div>
        
        <!-- Forms -->
        <div class="forms">
            <div class="form-card">
                <h3>➕ إضافة بطاقة</h3>
                <form method="POST" action="?action=add">
                    <input type="hidden" name="_token" value="<?= csrf() ?>">
                    <input type="text" name="number" pattern="\d{10}" placeholder="10 أرقام" required>
                    <input type="number" name="amount" placeholder="السعر" value="250" required>
                    <select name="speed" required>
                        <option value="512k/512k">512k/512k</option>
                        <option value="256k/1M" selected>256k/1M</option>
                        <option value="512k/2M">512k/2M</option>
                        <option value="2M/15M">2M/15M</option>
                    </select>
                    <input type="number" name="data_limit" placeholder="حجم MB" value="500">
                    <button type="submit">إضافة</button>
                </form>
            </div>
            
            <div class="form-card">
                <h3>🎲 توليد دفعة</h3>
                <form method="POST" action="?action=generate">
                    <input type="hidden" name="_token" value="<?= csrf() ?>">
                    <input type="number" name="count" placeholder="العدد" value="10" min="1" max="100" required>
                    <input type="number" name="amount" value="250" required>
                    <select name="speed" required>
                        <option value="512k/512k">512k/512k</option>
                        <option value="256k/1M" selected>256k/1M</option>
                        <option value="512k/2M">512k/2M</option>
                        <option value="2M/15M">2M/15M</option>
                    </select>
                    <input type="number" name="data_limit" placeholder="MB" value="500">
                    <button type="submit">توليد</button>
                </form>
            </div>
        </div>
        
        <!-- Cards Table -->
        <div class="table-card">
            <h3>📋 البطاقات (<?= count($cards) ?>)</h3>
            
            <?php if (empty($cards)): ?>
                <p class="empty">لا توجد بطاقات</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>السعر</th>
                            <th>السرعة</th>
                            <th>البيانات</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cards as $card): ?>
                            <tr>
                                <td class="mono"><?= e($card->number) ?></td>
                                <td><?= $card->amount ?> ريال</td>
                                <td><span class="badge"><?= e($card->speed) ?></span></td>
                                <td>
                                    <?php if ($card->dataLimitMb > 0): ?>
                                        <div class="progress">
                                            <div class="progress-bar" 
                                                 style="width: <?= $card->dataPercentage() ?>%"></div>
                                        </div>
                                        <small><?= $card->dataUsedMb ?>/<?= $card->dataLimitMb ?> MB</small>
                                    <?php else: ?>
                                        <span class="muted">غير محدود</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status status-<?= $card->status ?>">
                                        <?= match($card->status) {
                                            'active' => '✅ نشطة',
                                            'unused' => '⏸️ غير مستخدمة',
                                            'expired' => '⌛ منتهية',
                                            'stopped' => '🛑 موقوفة',
                                            default => $card->status,
                                        } ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($card->isAvailable()): ?>
                                        <form method="POST" action="?action=activate" style="display:inline">
                                            <input type="hidden" name="_token" value="<?= csrf() ?>">
                                            <input type="hidden" name="number" value="<?= e($card->number) ?>">
                                            <button class="btn-icon">⚡</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="?action=delete" style="display:inline"
                                          onsubmit="return confirm('حذف؟')">
                                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                                        <input type="hidden" name="number" value="<?= e($card->number) ?>">
                                        <button class="btn-icon">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
    
    <footer>YHM v0.2 © 2026 — Made in Yemen 🇾🇪</footer>
</body>
</html>
