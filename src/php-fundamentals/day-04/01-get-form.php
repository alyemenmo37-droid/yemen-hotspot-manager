<?php
/**
 * Day 4 - GET Form
 * YHM Project
 */

$name = $_GET['name'] ?? '';
$card = $_GET['card'] ?? '';

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>YHM - GET Form</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; padding: 20px; }
        .container { max-width: 500px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #009639; text-align: center; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { background: #009639; color: white; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; margin-top: 15px; width: 100%; font-size: 16px; }
        button:hover { background: #007a2e; }
        .result { margin-top: 20px; padding: 15px; background: #e8f5e9; border-right: 4px solid #009639; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📡 YHM - نموذج GET</h1>
        
        <form method="GET" action="">
            <label>الاسم:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="اسمك">
            
            <label>رقم البطاقة:</label>
            <input type="text" name="card" value="<?= htmlspecialchars($card) ?>" placeholder="1234567890">
            
            <button type="submit">إرسال</button>
        </form>
        
        <?php if ($name && $card): ?>
            <div class="result">
                <h3>✅ استلمنا:</h3>
                <p><strong>الاسم:</strong> <?= htmlspecialchars($name) ?></p>
                <p><strong>البطاقة:</strong> <?= htmlspecialchars($card) ?></p>
                <p><small>ملاحظة: البيانات في URL (غير آمنة للحساسة)</small></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
