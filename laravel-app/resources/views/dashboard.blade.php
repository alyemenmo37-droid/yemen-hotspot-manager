@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px;">
        <h1 style="color: #009639;">📊 لوحة التحكم</h1>
        <div style="color: #666; font-size: 14px;">
            🕐 {{ now()->translatedFormat('l، j F Y') }}
        </div>
    </div>
    
    {{-- Stats Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">🎫</div>
            <div style="font-size: 28px; font-weight: bold; color: #009639;">{{ $stats['total'] }}</div>
            <div style="color: #666; font-size: 13px;">إجمالي البطاقات</div>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">✅</div>
            <div style="font-size: 28px; font-weight: bold; color: #2e7d32;">{{ $stats['active'] }}</div>
            <div style="color: #666; font-size: 13px;">نشطة</div>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">⏸️</div>
            <div style="font-size: 28px; font-weight: bold; color: #f57c00;">{{ $stats['unused'] }}</div>
            <div style="color: #666; font-size: 13px;">غير مستخدمة</div>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); text-align: center;">
            <div style="font-size: 32px; margin-bottom: 5px;">💰</div>
            <div style="font-size: 28px; font-weight: bold; color: #c62828;">{{ number_format($stats['sales']) }}</div>
            <div style="color: #666; font-size: 13px;">المبيعات (ريال)</div>
        </div>
    </div>
    
    {{-- Quick Actions --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <h3 style="color: #009639; margin-bottom: 15px;">🚀 إجراءات سريعة</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="{{ route('cards.create') }}" 
               style="padding: 20px; background: #e8f5e9; border-radius: 10px; text-decoration: none; color: #2e7d32; text-align: center; transition: all 0.2s;">
                <div style="font-size: 32px; margin-bottom: 5px;">➕</div>
                <div style="font-weight: bold;">إضافة بطاقة</div>
            </a>
            <a href="{{ route('cards.index') }}" 
               style="padding: 20px; background: #e3f2fd; border-radius: 10px; text-decoration: none; color: #1565c0; text-align: center;">
                <div style="font-size: 32px; margin-bottom: 5px;">🎫</div>
                <div style="font-weight: bold;">قائمة البطاقات</div>
            </a>
            <a href="{{ route('statistics') }}" 
               style="padding: 20px; background: #fff3e0; border-radius: 10px; text-decoration: none; color: #e65100; text-align: center;">
                <div style="font-size: 32px; margin-bottom: 5px;">📊</div>
                <div style="font-weight: bold;">الإحصائيات</div>
            </a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.users.index') }}" 
                   style="padding: 20px; background: #ffebee; border-radius: 10px; text-decoration: none; color: #c62828; text-align: center;">
                    <div style="font-size: 32px; margin-bottom: 5px;">👥</div>
                    <div style="font-weight: bold;">إدارة المستخدمين</div>
                </a>
            @endif
        </div>
    </div>
    
    {{-- Recent Cards --}}
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <h3 style="color: #009639; margin-bottom: 15px;">📋 آخر البطاقات المضافة</h3>
        
        @php
            $recentCards = \App\Models\Card::with('speedLevel.category')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        @endphp
        
        @if($recentCards->isEmpty())
            <p style="color: #999; text-align: center; padding: 20px;">لا توجد بطاقات</p>
        @else
            @foreach($recentCards as $card)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-family: monospace; font-weight: bold;">{{ $card->number }}</span>
                        <span style="background: #e3f2fd; color: #1565c0; padding: 3px 10px; border-radius: 10px; font-size: 11px;">
                            {{ $card->speed }}
                        </span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-weight: bold;">{{ $card->amount }} ريال</span>
                        <span style="color: #999; font-size: 12px;">{{ $card->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        @endif
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="{{ route('cards.index') }}" style="color: #009639; text-decoration: none; font-weight: bold;">
                عرض كل البطاقات →
            </a>
        </div>
    </div>
@endsection
