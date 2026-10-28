@extends('layouts.app')

@section('title', 'MikroTik')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h1 style="color: #009639;">🌐 MikroTik Monitor</h1>
        <form method="POST" action="{{ route('mikrotik.sync') }}" style="display: inline;">
            @csrf
            <button type="submit" style="background: #f57c00; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                🔄 مزامنة كل البطاقات
            </button>
        </form>
    </div>
    
    @php
        $isMock = config('mikrotik.mock', true);
        $totalUsers = app(\App\Services\Mikrotik\MockManager::class)->getUsersCount();
        $totalSessions = count($sessions);
    @endphp
    
    {{-- Mode Banner --}}
    <div style="padding: 15px; border-radius: 10px; margin-bottom: 20px; background: {{ $isMock ? '#fff3e0' : '#e8f5e9' }}; color: {{ $isMock ? '#e65100' : '#2e7d32' }}; border-right: 4px solid {{ $isMock ? '#e65100' : '#2e7d32' }};">
        @if($isMock)
            🟡 <strong>وضع المحاكاة (Mock Mode)</strong>
        @else
            🟢 <strong>متصل بـ MikroTik</strong>
        @endif
        — إجمالي البطاقات في الراوتر: <strong>{{ $totalUsers }}</strong>
    </div>
    
    {{-- System Info --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <h3 style="color: #009639; margin-bottom: 15px;">🖥️ معلومات النظام</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
            @foreach(['identity' => '🏷️ الاسم', 'version' => '📦 الإصدار', 'uptime' => '⏱️ Uptime', 'cpu-load' => '💻 المعالج', 'users_count' => '🎫 البطاقات'] as $key => $label)
                @if(isset($info[$key]))
                    <div style="padding: 15px; background: #f9f9f9; border-radius: 8px;">
                        <div style="font-size: 12px; color: #666;">{{ $label }}</div>
                        <div style="font-weight: bold; margin-top: 5px;">{{ $info[$key] }}</div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    
    {{-- Active Sessions --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="color: #009639; margin: 0;">
                👥 المتصلون الآن (<span id="sessions-count">{{ $totalSessions }}</span>)
            </h3>
            <div style="font-size: 12px; color: #999;">
                🟢 آخر تحديث: <span id="last-refresh">{{ now()->format('H:i:s') }}</span>
            </div>
        </div>
        
        <div id="sessions-container">
            {{-- المحتوى الأصلي --}}
            @if(empty($sessions))
                <p style="text-align: center; padding: 40px; color: #999;">
                    😴 لا يوجد متصلون حالياً
                </p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 12px; text-align: right;">المستخدم</th>
                            <th style="padding: 12px; text-align: right;">IP</th>
                            <th style="padding: 12px; text-align: right;">المدة</th>
                            <th style="padding: 12px; text-align: right;">تحميل</th>
                            <th style="padding: 12px; text-align: right;">رفع</th>
                            <th style="padding: 12px; text-align: right;">إجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px; font-family: monospace; font-weight: bold;">{{ $session['user'] ?? '-' }}</td>
                                <td style="padding: 12px;">{{ $session['address'] ?? '-' }}</td>
                                <td style="padding: 12px;">{{ $session['uptime'] ?? '-' }}</td>
                                <td style="padding: 12px; color: #1565c0;">
                                    {{ isset($session['bytes-in']) ? number_format($session['bytes-in'] / 1024 / 1024, 1) . ' MB' : '-' }}
                                </td>
                                <td style="padding: 12px; color: #c62828;">
                                    {{ isset($session['bytes-out']) ? number_format($session['bytes-out'] / 1024 / 1024, 1) . ' MB' : '-' }}
                                </td>
                                <td style="padding: 12px;">
                                    <form method="POST" action="{{ route('mikrotik.disconnect', $session['user'] ?? '') }}" style="display:inline">
                                        @csrf
                                        <button type="submit" style="background: #c62828; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                                            🔌 فصل
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    
    {{-- Test Actions --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-top: 20px;">
        <h3 style="color: #009639; margin-bottom: 15px;">🧪 اختبار سريع</h3>
        <p style="color: #666; margin-bottom: 15px; font-size: 14px;">
            محاكاة تسجيل دخول مستخدم (لاختبار النظام):
        </p>
        
        @php
            $mockUsers = app(\App\Services\Mikrotik\MockManager::class)->getUsers();
            $availableUsers = array_diff(array_keys($mockUsers), array_column($sessions, 'user'));
        @endphp
        
        @if(empty($availableUsers))
            <p style="color: #f57c00;">⚠️ لا يوجد مستخدمون متاحون (كل البطاقات متصلة أو لا توجد بطاقات)</p>
            <p style="color: #666; font-size: 13px; margin-top: 10px;">
                💡 أضف بطاقة من <a href="{{ route('cards.create') }}" style="color: #009639;">هنا</a>
            </p>
        @else
            <form method="POST" action="{{ route('mikrotik.connect', $availableUsers[array_key_first($availableUsers)]) }}">
                @csrf
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <select name="card" onchange="this.form.action = '{{ route('mikrotik.connect', 'CARD') }}'.replace('CARD', this.value);"
                            style="flex: 1; min-width: 200px; padding: 12px; border: 2px solid #eee; border-radius: 8px;">
                        @foreach($availableUsers as $user)
                            <option value="{{ $user }}">{{ $user }}</option>
                        @endforeach
                    </select>
                    <button type="submit" style="background: #009639; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                        🔌 تسجيل دخول
                    </button>
                </div>
            </form>
        @endif
    </div>

{{-- Live Update Script --}}
@push('scripts')
<script>
const updateInterval = 5000; // 5 ثواني
let intervalId = null;
let lastUpdate = null;

// ═══════════════════════════════════════
// تحديث بيانات الجلسات
// ═══════════════════════════════════════
async function refreshData() {
    try {
        const response = await fetch('{{ route("api.mikrotik.status") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        
        const data = await response.json();
        
        if (data.success) {
            updateSessionsTable(data.sessions);
            updateSessionsCount(data.sessions_count);
            updateLastRefresh();
        }
    } catch (error) {
        console.error('Refresh failed:', error);
    }
}

// ═══════════════════════════════════════
// تحديث الجدول
// ═══════════════════════════════════════
function updateSessionsTable(sessions) {
    const container = document.getElementById('sessions-container');
    if (!container) return;
    
    if (sessions.length === 0) {
        container.innerHTML = `
            <p style="text-align: center; padding: 40px; color: #999;">
                😴 لا يوجد متصلون حالياً
            </p>
        `;
        return;
    }
    
    let html = `
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 12px; text-align: right;">المستخدم</th>
                        <th style="padding: 12px; text-align: right;">IP</th>
                        <th style="padding: 12px; text-align: right;">المدة</th>
                        <th style="padding: 12px; text-align: right;">تحميل</th>
                        <th style="padding: 12px; text-align: right;">رفع</th>
                        <th style="padding: 12px; text-align: right;">إجراء</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    sessions.forEach(session => {
        const bytesIn = session['bytes-in'] ? (session['bytes-in'] / 1024 / 1024).toFixed(1) + ' MB' : '-';
        const bytesOut = session['bytes-out'] ? (session['bytes-out'] / 1024 / 1024).toFixed(1) + ' MB' : '-';
        
        html += `
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 12px; font-family: monospace; font-weight: bold;">${session.user || '-'}</td>
                <td style="padding: 12px;">${session.address || '-'}</td>
                <td style="padding: 12px;">${session.uptime || '-'}</td>
                <td style="padding: 12px; color: #1565c0;">${bytesIn}</td>
                <td style="padding: 12px; color: #c62828;">${bytesOut}</td>
                <td style="padding: 12px;">
                    <form method="POST" action="/mikrotik/disconnect/${session.user}" style="display:inline">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" style="background: #c62828; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px;">
                            🔌 فصل
                        </button>
                    </form>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// ═══════════════════════════════════════
// تحديث العداد
// ═══════════════════════════════════════
function updateSessionsCount(count) {
    const el = document.getElementById('sessions-count');
    if (el) el.textContent = count;
}

// ═══════════════════════════════════════
// وقت آخر تحديث
// ═══════════════════════════════════════
function updateLastRefresh() {
    const el = document.getElementById('last-refresh');
    if (el) {
        const now = new Date();
        el.textContent = now.toLocaleTimeString('ar-YE');
    }
}

// ═══════════════════════════════════════
// بدء/إيقاف
// ═══════════════════════════════════════
function startPolling() {
    if (intervalId) return;
    intervalId = setInterval(refreshData, updateInterval);
    console.log('✅ Live updates started');
}

function stopPolling() {
    if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
        console.log('⏸️ Live updates stopped');
    }
}

// ═══════════════════════════════════════
// بدء تلقائي
// ═══════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    startPolling();
    refreshData(); // أول تحديث فوري
});

// إيقاف عند مغادرة الصفحة
window.addEventListener('beforeunload', stopPolling);
</script>
@endpush
@endsection
