@extends('layouts.app')

@section('title', 'الإحصائيات')

@section('content')
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px;">
        <h1 style="color: #009639;">📊 الإحصائيات</h1>
        <a href="{{ route('dashboard') }}" style="color: #666; text-decoration: none;">← العودة</a>
    </div>
    
    {{-- 1. Stats Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">🎫</div>
            <div style="font-size: 28px; font-weight: bold; color: #009639;">{{ $general['total_cards'] }}</div>
            <div style="color: #666; font-size: 13px;">إجمالي البطاقات</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">✅</div>
            <div style="font-size: 28px; font-weight: bold; color: #2e7d32;">{{ $general['active_cards'] }}</div>
            <div style="color: #666; font-size: 13px;">نشطة</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">💰</div>
            <div style="font-size: 28px; font-weight: bold; color: #c62828;">{{ number_format($general['total_sales']) }}</div>
            <div style="color: #666; font-size: 13px;">المبيعات (ريال)</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">👥</div>
            <div style="font-size: 28px; font-weight: bold; color: #1565c0;">{{ $general['total_users'] }}</div>
            <div style="color: #666; font-size: 13px;">المستخدمون</div>
        </div>
    </div>
    
    {{-- 2. Sales by Category --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <h3 style="color: #009639; margin-bottom: 20px;">📁 المبيعات حسب الفئة</h3>
        
        @if($salesByCategory->isEmpty())
            <p style="color: #999; text-align: center; padding: 20px;">لا توجد بيانات</p>
        @else
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                @foreach($salesByCategory as $cat)
                    <div style="padding: 20px; background: {{ $cat->color }}22; border-radius: 10px; border-right: 4px solid {{ $cat->color }};">
                        <div style="font-weight: bold; color: {{ $cat->color }}; font-size: 16px; margin-bottom: 10px;">
                            {{ $cat->name }}
                        </div>
                        <div style="font-size: 24px; font-weight: bold; margin-bottom: 5px;">{{ $cat->cards_count }} بطاقة</div>
                        <div style="color: #666; font-size: 13px;">{{ number_format($cat->total_sales) }} ريال</div>
                        <div style="color: #999; font-size: 12px; margin-top: 5px;">متوسط: {{ number_format($cat->avg_price) }} ريال</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    {{-- 3. Daily Sales --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <h3 style="color: #009639; margin-bottom: 20px;">📅 المبيعات اليومية (آخر 7 أيام)</h3>
        
        @if($dailySales->isEmpty())
            <p style="color: #999; text-align: center; padding: 20px;">لا توجد بيانات</p>
        @else
            <div style="display: flex; gap: 8px; align-items: flex-end; height: 200px; padding: 20px 0;">
                @php
                    $maxRevenue = $dailySales->max('revenue') ?: 1;
                @endphp
                @foreach($dailySales as $day)
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 5px;">
                        <div style="font-size: 11px; color: #666;">{{ number_format($day->revenue) }}</div>
                        <div style="width: 100%; background: linear-gradient(to top, #009639, #4caf50); border-radius: 4px 4px 0 0; height: {{ max(10, ($day->revenue / $maxRevenue) * 150) }}px; min-height: 10px;"></div>
                        <div style="font-size: 11px; color: #999;">{{ \Carbon\Carbon::parse($day->date)->format('m/d') }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    
    {{-- 4. Status Distribution + Top Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        {{-- Status Distribution --}}
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <h3 style="color: #009639; margin-bottom: 20px;">📊 توزيع الحالات</h3>
            
            @php
                $statusConfig = [
                    'active' => ['color' => '#2e7d32', 'label' => '✅ نشطة'],
                    'unused' => ['color' => '#f57c00', 'label' => '⏸️ غير مستخدمة'],
                    'expired' => ['color' => '#c62828', 'label' => '⌛ منتهية'],
                    'stopped' => ['color' => '#666', 'label' => '🛑 موقوفة'],
                ];
            @endphp
            
            @foreach($statusDistribution as $status)
                @php
                    $cfg = $statusConfig[$status->status] ?? ['color' => '#999', 'label' => $status->status];
                    $percent = $general['total_cards'] > 0 ? round(($status->count / $general['total_cards']) * 100, 1) : 0;
                @endphp
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px;">
                        <span style="color: #333;">{{ $cfg['label'] }}</span>
                        <span style="color: #666;">{{ $status->count }} ({{ $percent }}%)</span>
                    </div>
                    <div style="background: #eee; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div style="background: {{ $cfg['color'] }}; height: 100%; width: {{ $percent }}%; border-radius: 4px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Top Cards --}}
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <h3 style="color: #009639; margin-bottom: 20px;">🏆 أعلى 5 بطاقات سعراً</h3>
            
            @foreach($topCards as $i => $card)
                <div style="display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #eee;">
                    <span style="width: 24px; height: 24px; background: #009639; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold;">
                        {{ $i + 1 }}
                    </span>
                    <div style="flex: 1;">
                        <div style="font-family: monospace; font-weight: bold; font-size: 13px;">{{ $card->number }}</div>
                        <div style="font-size: 11px; color: #666;">
                            {{ $card->speedLevel->category->name ?? '-' }} • {{ $card->speed }}
                        </div>
                    </div>
                    <div style="font-weight: bold; color: #c62828;">{{ $card->amount }} ريال</div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
