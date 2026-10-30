<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير البطاقات</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; padding: 20px; font-size: 12px; }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #009639;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header h1 { color: #009639; font-size: 24px; margin-bottom: 5px; }
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
        .stat .value { font-size: 20px; font-weight: bold; color: #009639; }
        .stat .label { color: #666; font-size: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th {
            background: #009639;
            color: white;
            padding: 8px;
            text-align: right;
            font-size: 11px;
        }
        td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
        tr:nth-child(even) { background: #f9f9f9; }
        
        .status-active { color: #2e7d32; font-weight: bold; }
        .status-unused { color: #f57c00; font-weight: bold; }
        .status-expired { color: #c62828; font-weight: bold; }
        .status-stopped { color: #666; font-weight: bold; }
        
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
        <div class="meta">تقرير البطاقات — {{ now()->format('Y-m-d H:i') }}</div>
    </div>
    
    <div class="stats">
        <div class="stat">
            <div class="value">{{ $stats['total'] }}</div>
            <div class="label">إجمالي البطاقات</div>
        </div>
        <div class="stat">
            <div class="value">{{ $stats['active'] }}</div>
            <div class="label">نشطة</div>
        </div>
        <div class="stat">
            <div class="value">{{ number_format($stats['sales']) }}</div>
            <div class="label">المبيعات (ريال)</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>الرقم</th>
                <th>السعر</th>
                <th>السرعة</th>
                <th>الحالة</th>
                <th>الفئة</th>
                <th>البيانات</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cards as $i => $card)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $card->number }}</td>
                    <td>{{ $card->amount }} ريال</td>
                    <td>{{ $card->speed }}</td>
                    <td class="status-{{ $card->status }}">
                        @switch($card->status)
                            @case('active') ✅ نشطة @break
                            @case('unused') ⏸️ غير مستخدمة @break
                            @case('expired') ⌛ منتهية @break
                            @case('stopped') 🛑 موقوفة @break
                        @endswitch
                    </td>
                    <td>{{ $card->speedLevel?->category?->name ?? '-' }}</td>
                    <td>
                        @if($card->data_limit_mb)
                            {{ $card->data_used_mb }}/{{ $card->data_limit_mb }} MB
                        @else
                            غير محدود
                        @endif
                    </td>
                    <td>{{ $card->created_at?->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        YHM v2.0 — صُنع بحب في اليمن 🇾🇪
    </div>
</body>
</html>
