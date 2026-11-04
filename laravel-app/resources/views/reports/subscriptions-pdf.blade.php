<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الاشتراكات</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; padding: 20px; }
        h1 { color: #009639; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #009639; color: white; padding: 10px; }
        td { padding: 8px; border-bottom: 1px solid #eee; text-align: right; }
        .active { color: #2e7d32; font-weight: bold; }
        .expired { color: #c62828; font-weight: bold; }
    </style>
</head>
<body>
    <h1>📅 تقرير الاشتراكات</h1>
    <p style="text-align:center; color:#666;">التاريخ: {{ now()->format('Y-m-d H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>المستخدم</th>
                <th>السرعة</th>
                <th>البداية</th>
                <th>النهاية</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $i => $sub)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $sub->user->name ?? '-' }}</td>
                <td>{{ $sub->speedLevel->name ?? '-' }}</td>
                <td>{{ $sub->starts_at->format('Y-m-d') }}</td>
                <td>{{ $sub->ends_at->format('Y-m-d') }}</td>
                <td class="{{ $sub->isActive() ? 'active' : 'expired' }}">
                    {{ $sub->isActive() ? '✅ نشط' : '⌛ منتهي' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;">لا توجد اشتراكات</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
