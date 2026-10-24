@extends('layouts.app')

@section('title', "تعديل {$card->number}")

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: #f57c00; margin-bottom: 20px;">
            ✏️ تعديل البطاقة
        </h1>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <form method="POST" action="{{ route('cards.update', $card) }}">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        رقم البطاقة:
                    </label>
                    <input type="text" value="{{ $card->number }}" disabled
                           style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; background: #f5f5f5; box-sizing: border-box;">
                    <small style="color: #999;">لا يمكن تعديل رقم البطاقة</small>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        السعر:
                    </label>
                    <input type="number" name="amount" value="{{ old('amount', $card->amount) }}" required
                           style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        السرعة:
                    </label>
                    <select name="speed" required
                            style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                        @foreach(['512k/512k', '256k/1M', '512k/2M', '2M/15M'] as $speed)
                            <option value="{{ $speed }}" {{ $card->speed === $speed ? 'selected' : '' }}>
                                {{ $speed }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        الحالة:
                    </label>
                    <select name="status" required
                            style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; box-sizing: border-box;">
                        <option value="unused" {{ $card->status === 'unused' ? 'selected' : '' }}>غير مستخدمة</option>
                        <option value="active" {{ $card->status === 'active' ? 'selected' : '' }}>نشطة</option>
                        <option value="expired" {{ $card->status === 'expired' ? 'selected' : '' }}>منتهية</option>
                        <option value="stopped" {{ $card->status === 'stopped' ? 'selected' : '' }}>موقوفة</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <x-button type="submit" style="flex: 1;">
                        💾 حفظ
                    </x-button>
                    <x-button href="{{ route('cards.index') }}" variant="secondary">
                        إلغاء
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
