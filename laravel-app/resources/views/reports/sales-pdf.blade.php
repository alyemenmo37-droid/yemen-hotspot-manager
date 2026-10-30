<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير المبيعات</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; padding: 20px; font-size: 12px; }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #009639;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 { color: #009639; font-size: 24px; }
        .header .meta { color: #666; font-size: 11px; }
        
        .stats {
            display: flex;
            justify-content: space-around;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .stat { text-align: center; }
        .stat .value { font-size: 24px; font-weight: bold; color: #009639; }
        .stat .label { color: #666; font-size: 10px; }
        
        h2 { color: #009639; font-size: 16px; margin: 20px 0 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th {
            background: #009639;
            color: white;
            padding: 8px;
            text-align: right;
            font-size: 11px;
        }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
        tr:nth-child(even) { background: #f9f9f9; }
        
        .total-row { background: #e8f5e9 !important; font-weight: bold; }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            color: #999;
            font-size: 9px;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📡 Yemen Hotspot Manager</h1>
        <div class="meta">تقرير المبيعات — {{ now()->format('Y-m-d H:i') }}</div>
    </div>
    
    <div class="stats">
        <div class="stat">
            <div class="value">{{ $totals['cards'] }}</div>
            <div class="label">إجمالي البطاقات</div>
        </div>
        <div class="stat">
            <div class="value">{{ $totals['sold'] }}</div>
            <div class="label">مباعة</div>
        </div>
        <div class="stat">
            <div class="value">{{ number_format($totals['revenue']) }}</div>
            <div class="label">الإيرادات (ريال)</div>
        </div>
    </div>
    
    <h2>📁 المبيعات حسب الفئة</h2>
    <table>
        <thead>
            <tr>
                <th>الفئة</th>
                <th>عدد البطاقات</th>
                <th>المبيعات (ريال)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($byCategory as $cat)
                <tr>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->cards_count }}</td>
                    <td>{{ number_format((float)$cat->total_sales) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>الإجمالي</td>
                <td>{{ $totals['sold'] }}</td>
                <td>{{ number_format($totals['revenue']) }}</td>
            </tr>
        </tbody>
    </table>
    
    <h2>📅 المبيعات اليومية (آخر 30 يوم)</h2>
    <table>
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>البطاقات</th>
                <th>الإيرادات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailySales as $day)
                <tr>
                    <td>{{ $day->date }}</td>
                    <td>{{ $day->count }}</td>
                    <td>{{ number_format((float)$day->revenue) }} ريال</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        YHM v2.0 — صُنع بحب في اليمن 🇾🇪
    </div>
</body>
</html>
