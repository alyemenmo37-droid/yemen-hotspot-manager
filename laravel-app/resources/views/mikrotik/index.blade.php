@extends('layouts.app')

@section('title', 'MikroTik')

@section('content')
    <h1 style="color: #009639; margin-bottom: 20px;">🌐 MikroTik Monitor</h1>
    
    @php
        $isMock = config('mikrotik.mock', true);
    @endphp
    
    {{-- Mode Banner --}}
    <div style="padding: 15px; border-radius: 10px; margin-bottom: 20px; background: {{ $isMock ? '#fff3e0' : '#e8f5e9' }}; color: {{ $isMock ? '#e65100' : '#2e7d32' }}; border-right: 4px solid {{ $isMock ? '#e65100' : '#2e7d32' }};">
        @if($isMock)
            🟡 <strong>وضع المحاكاة (Mock Mode)</strong> — لا يوجد MikroTik حقيقي متصل. للاتصال الحقيقي، غيّر `MIKROTIK_MOCK=false` في `.env` وأضف بيانات الراوتر.
        @else
            🟢 <strong>متصل بـ MikroTik</strong> — البيانات حقيقية من الراوتر.
        @endif
    </div>
    
    {{-- System Info --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <h3 style="color: #009639; margin-bottom: 15px;">🖥️ معلومات النظام</h3>
        
        @if(empty($info))
            <p style="color: #999;">لا توجد معلومات</p>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                @foreach(['uptime' => '⏱️ Uptime', 'version' => '📦 الإصدار', 'cpu-load' => '💻 المعالج', 'identity' => '🏷️ الاسم'] as $key => $label)
                    @if(isset($info[$key]))
                        <div style="padding: 15px; background: #f9f9f9; border-radius: 8px;">
                            <div style="font-size: 12px; color: #666;">{{ $label }}</div>
                            <div style="font-weight: bold; margin-top: 5px;">{{ $info[$key] }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
    
    {{-- Active Sessions --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <h3 style="color: #009639; margin-bottom: 15px;">👥 المتصلون الآن ({{ count($sessions) }})</h3>
        
        @if(empty($sessions))
            <p style="text-align: center; padding: 40px; color: #999;">لا يوجد متصلون حالياً</p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 12px; text-align: right;">المستخدم</th>
                            <th style="padding: 12px; text-align: right;">IP</th>
                            <th style="padding: 12px; text-align: right;">MAC</th>
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
                                <td style="padding: 12px; font-family: monospace; font-size: 12px;">{{ $session['mac-address'] ?? '-' }}</td>
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
@endsection
