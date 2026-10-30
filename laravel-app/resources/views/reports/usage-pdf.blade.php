<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الاستهلاك</title>
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
        <div class="meta">تقرير الاستهلاك — {{ now()->format('Y-m-d H:i') }}</div>
    </div>
    
    <div class="stats">
        <div class="stat">
            <div class="value">{{ $overall['total_sessions'] }}</div>
            <div class="label">الجلسات</div>
        </div>
        <div class="stat">
            <div class="value">{{ $overall['unique_users'] }}</div>
            <div class="label">المستخدمون</div>
        </div>
        <div class="stat">
            <div class="value">{{ $overall['download_gb'] }} GB</div>
            <div class="label">التحميل</div>
        </div>
        <div class="stat">
            <div class="value">{{ $overall['upload_gb'] }} GB</div>
            <div class="label">الرفع</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>البطاقة</th>
                <th>الاستهلاك (MB)</th>
                <th>الاستهلاك (GB)</th>
                <th>الجلسات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topConsumers as $i => $consumer)
                @php
                    $mb = round($consumer->total_bytes / 1024 / 1024, 2);
                    $gb = round($consumer->total_bytes / 1024 / 1024 / 1024, 2);
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $consumer->username }}</td>
                    <td>{{ $mb }}</td>
                    <td>{{ $gb }}</td>
                    <td>{{ $consumer->sessions_count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        YHM v2.0 — صُنع بحب في اليمن 🇾🇪
    </div>
</body>
</html>
