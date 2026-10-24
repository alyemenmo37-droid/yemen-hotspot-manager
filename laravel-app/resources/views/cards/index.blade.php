@extends('layouts.app')

@section('title', 'البطاقات')

@section('content')
    {{-- Stats --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <x-stat-card title="إجمالي" :value="$stats['total']" icon="🎫" />
        <x-stat-card title="نشطة" :value="$stats['active']" icon="✅" color="#2e7d32" />
        <x-stat-card title="غير مستخدمة" :value="$stats['unused']" icon="⏸️" color="#f57c00" />
        <x-stat-card title="المبيعات" :value="number_format($stats['sales']) . ' ريال'" icon="💰" color="#c62828" />
    </div>
    
    {{-- Actions --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h1 style="color: #009639; font-size: 22px;">🎫 البطاقات ({{ $stats['total'] }})</h1>
        <x-button href="{{ route('cards.create') }}">
            ➕ إضافة بطاقة
        </x-button>
    </div>
    
    {{-- Desktop Table --}}
    <div class="cards-table-desktop" style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 15px; text-align: right; white-space: nowrap;">#</th>
                    <th style="padding: 15px; text-align: right; white-space: nowrap;">الرقم</th>
                    <th style="padding: 15px; text-align: right; white-space: nowrap;">السعر</th>
                    <th style="padding: 15px; text-align: right; white-space: nowrap;">السرعة</th>
                    <th style="padding: 15px; text-align: right; white-space: nowrap;">الحالة</th>
                    <th style="padding: 15px; text-align: right; white-space: nowrap;">التاريخ</th>
                    <th style="padding: 15px; text-align: right; white-space: nowrap;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cards as $card)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;">{{ $card->id }}</td>
                        <td style="padding: 15px; font-family: monospace; font-weight: bold; white-space: nowrap;">
                            {{ $card->number }}
                        </td>
                        <td style="padding: 15px; white-space: nowrap;">{{ $card->amount }} ريال</td>
                        <td style="padding: 15px; white-space: nowrap;">
                            <span style="background: #e3f2fd; color: #1565c0; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                                {{ $card->speed }}
                            </span>
                        </td>
                        <td style="padding: 15px; white-space: nowrap;">
                            <x-card-status :status="$card->status" />
                        </td>
                        <td style="padding: 15px; font-size: 12px; color: #666; white-space: nowrap;">
                            {{ $card->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td style="padding: 15px; white-space: nowrap;">
                            <a href="{{ route('cards.show', $card) }}" 
                               style="color: #009639; text-decoration: none; font-weight: bold;">
                                عرض
                            </a>
                            
                            @if($card->isAvailable())
                                |
                                <form method="POST" action="{{ route('cards.activate', $card) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: #2e7d32; cursor: pointer; font-weight: bold; padding: 0;">
                                        ⚡
                                    </button>
                                </form>
                            @endif
                            
                            |
                            <form method="POST" action="{{ route('cards.destroy', $card) }}" style="display:inline"
                                  onsubmit="return confirm('حذف البطاقة {{ $card->number }}؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #c62828; cursor: pointer; padding: 0;">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 60px; text-align: center; color: #999;">
                            📭 لا توجد بطاقات
                            <br><br>
                            <x-button href="{{ route('cards.create') }}">
                                إضافة أول بطاقة
                            </x-button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Mobile Cards --}}
    <div class="cards-mobile">
        @forelse($cards as $card)
            <div style="background: white; border-radius: 12px; padding: 15px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                {{-- Header --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #eee;">
                    <div style="font-family: monospace; font-weight: bold; font-size: 15px; direction: ltr;">
                        {{ $card->number }}
                    </div>
                    <x-card-status :status="$card->status" />
                </div>
                
                {{-- Details --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px;">
                    <div>
                        <div style="font-size: 11px; color: #999; margin-bottom: 3px;">السعر</div>
                        <div style="font-weight: bold;">{{ $card->amount }} ريال</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #999; margin-bottom: 3px;">السرعة</div>
                        <span style="background: #e3f2fd; color: #1565c0; padding: 3px 10px; border-radius: 10px; font-size: 12px; font-weight: bold;">
                            {{ $card->speed }}
                        </span>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #999; margin-bottom: 3px;">#</div>
                        <div>{{ $card->id }}</div>
                    </div>
                    <div>
                        <div style="font-size: 11px; color: #999; margin-bottom: 3px;">التاريخ</div>
                        <div style="font-size: 12px;">{{ $card->created_at->format('Y-m-d') }}</div>
                    </div>
                </div>
                
                {{-- Actions --}}
                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('cards.show', $card) }}" 
                       style="flex: 1; text-align: center; padding: 10px; background: #009639; color: white; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 13px;">
                        👁️ عرض
                    </a>
                    
                    @if($card->isAvailable())
                        <form method="POST" action="{{ route('cards.activate', $card) }}" style="flex: 1;">
                            @csrf
                            <button type="submit" style="width: 100%; padding: 10px; background: #2e7d32; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 13px; cursor: pointer;">
                                ⚡ تفعيل
                            </button>
                        </form>
                    @endif
                    
                    <form method="POST" action="{{ route('cards.destroy', $card) }}" style="flex: 1;"
                          onsubmit="return confirm('حذف البطاقة {{ $card->number }}؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="width: 100%; padding: 10px; background: #c62828; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 13px; cursor: pointer;">
                            🗑️ حذف
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div style="background: white; border-radius: 12px; padding: 60px; text-align: center; color: #999;">
                📭 لا توجد بطاقات
                <br><br>
                <x-button href="{{ route('cards.create') }}">
                    إضافة أول بطاقة
                </x-button>
            </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    <div style="margin-top: 20px;">
        {{ $cards->links() }}
    </div>
@endsection

@push('styles')
<style>
    /* Default: Desktop view */
    .cards-table-desktop { display: block; }
    .cards-mobile { display: none; }
    
    /* Mobile: < 768px */
    @media (max-width: 768px) {
        .cards-table-desktop { display: none; }
        .cards-mobile { display: block; }
    }
    
    /* Pagination styling */
    nav[role="navigation"] svg { width: 20px; height: 20px; }
    nav[role="navigation"] a, nav[role="navigation"] span {
        padding: 8px 12px;
        margin: 0 2px;
        border-radius: 6px;
        background: white;
        color: #333;
        text-decoration: none;
        display: inline-block;
    }
    nav[role="navigation"] span[aria-current="page"] {
        background: #009639;
        color: white;
    }
</style>
@endpush
