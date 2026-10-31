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
    
    {{-- Header + Search --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
        <h1 style="color: #009639; font-size: 22px;">🎫 البطاقات ({{ $cards->total() }})</h1>
        <x-button href="{{ route('cards.create') }}">➕ إضافة</x-button>
    </div>
    
    {{-- Advanced Filters --}}
    <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <form method="GET" action="{{ route('cards.index') }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                {{-- Search --}}
                <div>
                    <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px; font-weight: bold;">
                        🔍 بحث (رقم البطاقة):
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="اكتب الرقم..."
                           style="width: 100%; padding: 10px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                </div>
                
                {{-- Status --}}
                <div>
                    <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px; font-weight: bold;">
                        📌 الحالة:
                    </label>
                    <select name="status" style="width: 100%; padding: 10px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                        <option value="">كل الحالات</option>
                        <option value="unused" {{ request('status') === 'unused' ? 'selected' : '' }}>⏸️ غير مستخدمة</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>✅ نشطة</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>⌛ منتهية</option>
                        <option value="stopped" {{ request('status') === 'stopped' ? 'selected' : '' }}>🛑 موقوفة</option>
                    </select>
                </div>
                
                {{-- Category --}}
                <div>
                    <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px; font-weight: bold;">
                        📁 الفئة:
                    </label>
                    <select name="category_id" style="width: 100%; padding: 10px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                        <option value="">كل الفئات</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Price Range --}}
                <div>
                    <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px; font-weight: bold;">
                        💰 السعر (من - إلى):
                    </label>
                    <div style="display: flex; gap: 5px;">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" 
                               placeholder="من" style="width: 50%; padding: 10px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" 
                               placeholder="إلى" style="width: 50%; padding: 10px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                    </div>
                </div>
                
                {{-- Sort --}}
                <div>
                    <label style="display: block; font-size: 12px; color: #666; margin-bottom: 5px; font-weight: bold;">
                        📊 الترتيب:
                    </label>
                    <select name="sort" style="width: 100%; padding: 10px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>الأحدث</option>
                        <option value="amount" {{ request('sort') === 'amount' ? 'selected' : '' }}>السعر</option>
                        <option value="number" {{ request('sort') === 'number' ? 'selected' : '' }}>رقم البطاقة</option>
                        <option value="status" {{ request('sort') === 'status' ? 'selected' : '' }}>الحالة</option>
                    </select>
                </div>
            </div>
            
            <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit" style="background: #009639; color: white; border: none; padding: 10px 25px; border-radius: 8px; font-weight: bold; cursor: pointer;">
                    🔍 تطبيق الفلاتر
                </button>
                <a href="{{ route('cards.index') }}" style="background: #666; color: white; padding: 10px 25px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                    🔄 مسح
                </a>
            </div>
        </form>
    </div>
    
    {{-- Bulk Actions Bar --}}
    <div id="bulk-bar" style="display: none; background: #fff3e0; padding: 15px; border-radius: 10px; margin-bottom: 15px; border-right: 4px solid #f57c00;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <span style="color: #e65100; font-weight: bold;">
                ✅ <span id="selected-count">0</span> بطاقة محددة
            </span>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button onclick="bulkAction('activate')" style="background: #2e7d32; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    ⚡ تفعيل
                </button>
                <button onclick="bulkAction('expire')" style="background: #c62828; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    ⌛ إنهاء
                </button>
                <button onclick="bulkAction('delete')" style="background: #b71c1c; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    🗑️ حذف
                </button>
            </div>
        </div>
    </div>
    
    {{-- Table --}}
    <div class="cards-table-desktop" style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto;">
        <form method="POST" action="{{ route('cards.bulk-action') }}" id="bulk-form">
            @csrf
            <input type="hidden" name="action" id="bulk-action-input">
            
            <table style="width: 100%; border-collapse: collapse; min-width: 900px;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="padding: 12px; text-align: center; width: 40px;">
                            <input type="checkbox" id="select-all" onchange="toggleAll(this)">
                        </th>
                        <th style="padding: 12px; text-align: right;">#</th>
                        <th style="padding: 12px; text-align: right;">الرقم</th>
                        <th style="padding: 12px; text-align: right;">السعر</th>
                        <th style="padding: 12px; text-align: right;">السرعة</th>
                        <th style="padding: 12px; text-align: right;">الفئة</th>
                        <th style="padding: 12px; text-align: right;">الحالة</th>
                        <th style="padding: 12px; text-align: right;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cards as $card)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px; text-align: center;">
                                <input type="checkbox" name="cards[]" value="{{ $card->id }}" class="card-checkbox" onchange="updateBulkBar()">
                            </td>
                            <td style="padding: 12px;">{{ $card->id }}</td>
                            <td style="padding: 12px; font-family: monospace; font-weight: bold;">{{ $card->number }}</td>
                            <td style="padding: 12px;">{{ $card->amount }} ريال</td>
                            <td style="padding: 12px;">
                                <span style="background: #e3f2fd; color: #1565c0; padding: 3px 10px; border-radius: 10px; font-size: 12px;">
                                    {{ $card->speed }}
                                </span>
                            </td>
                            <td style="padding: 12px; font-size: 13px;">{{ $card->speedLevel?->category?->name ?? '-' }}</td>
                            <td style="padding: 12px;"><x-card-status :status="$card->status" /></td>
                            <td style="padding: 12px; white-space: nowrap;">
                                <a href="{{ route('cards.show', $card) }}" style="color: #009639; text-decoration: none; font-weight: bold;">عرض</a>
                                @if($card->isAvailable())
                                    | <form method="POST" action="{{ route('cards.activate', $card) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" style="background: none; border: none; color: #2e7d32; cursor: pointer; font-weight: bold; padding: 0;">⚡</button>
                                    </form>
                                @endif
                                | <form method="POST" action="{{ route('cards.destroy', $card) }}" style="display:inline"
                                        onsubmit="return confirm('حذف {{ $card->number }}؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #c62828; cursor: pointer; padding: 0;">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 60px; text-align: center; color: #999;">
                                📭 لا توجد بطاقات مطابقة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
    </div>
    
    {{-- Pagination --}}
    <div style="margin-top: 20px;">
        {{ $cards->links() }}
    </div>
@endsection

@push('scripts')
<script>
function toggleAll(checkbox) {
    document.querySelectorAll('.card-checkbox').forEach(cb => cb.checked = checkbox.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const selected = document.querySelectorAll('.card-checkbox:checked').length;
    const bar = document.getElementById('bulk-bar');
    const count = document.getElementById('selected-count');
    
    if (selected > 0) {
        bar.style.display = 'block';
        count.textContent = selected;
    } else {
        bar.style.display = 'none';
    }
}

function bulkAction(action) {
    const selected = document.querySelectorAll('.card-checkbox:checked').length;
    if (selected === 0) return;
    
    const actionNames = { activate: 'تفعيل', expire: 'إنهاء', delete: 'حذف' };
    const message = `هل أنت متأكد من ${actionNames[action]} ${selected} بطاقة؟`;
    
    if (!confirm(message)) return;
    
    document.getElementById('bulk-action-input').value = action;
    document.getElementById('bulk-form').submit();
}
</script>
@endpush
