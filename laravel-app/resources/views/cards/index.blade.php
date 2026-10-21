<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>YHM - البطاقات</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        header {
            background: linear-gradient(135deg, #009639, #007a2e);
            color: white;
            padding: 25px;
            text-align: center;
        }
        header h1 { font-size: 24px; }
        header p { opacity: 0.9; margin-top: 5px; }
        main {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .actions-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn {
            background: #009639;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        .btn:hover { background: #007a2e; }
        .btn-secondary { background: #666; }
        .btn-secondary:hover { background: #444; }
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f5f5f5;
            padding: 15px;
            text-align: right;
            font-size: 13px;
            color: #666;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        tr:hover td { background: #fafafa; }
        .badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-active { background: #e8f5e9; color: #2e7d32; }
        .badge-unused { background: #fff3e0; color: #e65100; }
        .badge-expired { background: #ffebee; color: #c62828; }
        .mono { font-family: monospace; font-weight: bold; }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-right: 4px solid #2e7d32;
        }
    </style>
</head>
<body>
    <header>
        <h1>📡 Yemen Hotspot Manager</h1>
        <p>لوحة إدارة البطاقات</p>
    </header>
    
    <main>
        @if(session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif
        
        <div class="actions-bar">
            <a href="{{ route('cards.create') }}" class="btn">➕ إضافة بطاقة</a>
            <a href="{{ route('yhm') }}" class="btn btn-secondary">← الرئيسية</a>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الرقم</th>
                        <th>السعر</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $card)
                        <tr>
                            <td>{{ $card['id'] }}</td>
                            <td class="mono">{{ $card['number'] }}</td>
                            <td>{{ $card['amount'] }} ريال</td>
                            <td>
                                <span class="badge badge-{{ $card['status'] }}">
                                    @switch($card['status'])
                                        @case('active')
                                            ✅ نشطة
                                            @break
                                        @case('unused')
                                            ⏸️ غير مستخدمة
                                            @break
                                        @case('expired')
                                            ⌛ منتهية
                                            @break
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('cards.show', $card['id']) }}">عرض</a>
                                |
                                <a href="{{ route('cards.edit', $card['id']) }}">تعديل</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                                لا توجد بطاقات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
