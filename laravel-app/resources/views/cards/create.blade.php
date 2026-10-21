<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>YHM - إضافة بطاقة</title>
    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 { color: #009639; margin-bottom: 20px; }
        label { display: block; margin: 15px 0 5px; font-weight: bold; }
        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #009639;
        }
        button {
            background: #009639;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            width: 100%;
        }
        button:hover { background: #007a2e; }
        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ إضافة بطاقة جديدة</h1>
        
        <form method="POST" action="{{ route('cards.store') }}">
            @csrf
            
            <label>رقم البطاقة:</label>
            <input type="text" name="number" pattern="\d{10}" 
                   placeholder="10 أرقام" required>
            
            <label>السعر:</label>
            <input type="number" name="amount" value="250" required>
            
            <label>السرعة:</label>
            <select name="speed" required>
                <option value="512k/512k">512k/512k</option>
                <option value="256k/1M" selected>256k/1M</option>
                <option value="512k/2M">512k/2M</option>
                <option value="2M/15M">2M/15M</option>
            </select>
            
            <button type="submit">حفظ</button>
        </form>
        
        <a href="{{ route('cards.index') }}" class="back">← رجوع</a>
    </div>
</body>
</html>
