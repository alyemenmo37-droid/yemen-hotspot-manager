<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>YHM - عرض بطاقة</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container {
            max-width: 500px; margin: auto; background: white;
            padding: 30px; border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #009639; }
        .info { padding: 15px; background: #f9f9f9; border-radius: 8px; margin: 15px 0; }
        a { color: #009639; display: block; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📇 البطاقة #{{ $id }}</h1>
        <div class="info">
            <p>هذا عرض تجريبي للبطاقة ذات المعرّف: <strong>{{ $id }}</strong></p>
            <p>سيتم تحميل البيانات الفعلية من قاعدة البيانات قريباً.</p>
        </div>
        <a href="{{ route('cards.index') }}">← رجوع للقائمة</a>
    </div>
</body>
</html>
