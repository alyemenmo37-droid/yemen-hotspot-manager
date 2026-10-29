@extends('layouts.app')

@section('title', 'المحاسبة')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
        <h1 style="color: #009639;">📊 المحاسبة (RADIUS Accounting)</h1>
        <form method="POST" action="{{ route('radius.sync-accounting') }}" style="display: inline;">
            @csrf
            <button type="submit" style="background: #f57c00; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                🔄 مزامنة الآن
            </button>
        </form>
    </div>
    
    {{-- Overall Stats --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">🔗</div>
            <div style="font-size: 28px; font-weight: bold; color: #009639;">{{ $overall['total_sessions'] }}</div>
            <div style="color: #666; font-size: 13px;">إجمالي الجلسات</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">👥</div>
            <div style="font-size: 28px; font-weight: bold; color: #1565c0;">{{ $overall['unique_users'] }}</div>
            <div style="color: #666; font-size: 13px;">مستخدمون فريدون</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">⬇️</div>
            <div style="font-size: 28px; font-weight: bold; color: #2e7d32;">{{ $overall['download_gb'] }} GB</div>
            <div style="color: #666; font-size: 13px;">التحميل</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">⬆️</div>
            <div style="font-size: 28px; font-weight: bold; color: #c62828;">{{ $overall['upload_gb'] }} GB</div>
            <div style="color: #666; font-size: 13px;">الرفع</div>
        </div>
    </div>
    
    {{-- Top Consumers --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <h3 style="color: #009639; margin-bottom: 20px;">🏆 أعلى 20 مستهلكاً</h3>
        
        @if(empty($topConsumers))
            <p style="text-align: center; padding: 40px; color: #999;">لا توجد بيانات</p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th style="padding: 12px; text-align: right;">#</th>
                            <th style="padding: 12px; text-align: right;">البطاقة</th>
                            <th style="padding: 12px; text-align: right;">الاستهلاك</th>
                            <th style="padding: 12px; text-align: right;">الجلسات</th>
                            <th style="padding: 12px; text-align: right;">النسبة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topConsumers as $i => $consumer)
                            @php
                                $card = \App\Models\Card::where('number', $consumer->username)->first();
                                $gb = round($consumer->total_bytes / 1024 / 1024 / 1024, 2);
                                $mb = round($consumer->total_bytes / 1024 / 1024, 0);
                                $percent = $card && $card->data_limit_mb > 0 
                                    ? round(($card->data_used_mb / $card->data_limit_mb) * 100, 1) 
                                    : 0;
                            @endphp
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px;">
                                    <span style="width: 24px; height: 24px; background: {{ $i < 3 ? '#f57c00' : '#009639' }}; color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">
                                        {{ $i + 1 }}
                                    </span>
                                </td>
                                <td style="padding: 12px; font-family: monospace; font-weight: bold;">{{ $consumer->username }}</td>
                                <td style="padding: 12px;">
                                    {{ $gb >= 1 ? "$gb GB" : "$mb MB" }}
                                </td>
                                <td style="padding: 12px;">{{ $consumer->sessions_count }}</td>
                                <td style="padding: 12px;">
                                    <div style="background: #eee; height: 8px; border-radius: 4px; overflow: hidden; min-width: 100px;">
                                        <div style="background: {{ $percent > 90 ? '#c62828' : ($percent > 70 ? '#f57c00' : '#009639') }}; height: 100%; width: {{ min(100, $percent) }}%;"></div>
                                    </div>
                                    <div style="font-size: 11px; color: #666; margin-top: 3px;">{{ $percent }}%</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
