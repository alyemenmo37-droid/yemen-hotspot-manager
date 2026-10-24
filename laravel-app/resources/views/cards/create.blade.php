@extends('layouts.app')

@section('title', 'إضافة بطاقة')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: #009639; margin-bottom: 20px;">➕ إضافة بطاقة جديدة</h1>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <form method="POST" action="{{ route('cards.store') }}">
                @csrf
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        رقم البطاقة:
                    </label>
                    <input type="text" 
                           name="number" 
                           pattern="\d{10}" 
                           placeholder="10 أرقام"
                           value="{{ old('number') }}"
                           required
                           style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('number') ? '#c62828' : '#eee' }}; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                    @error('number')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">
                            ❌ {{ $message }}
                        </div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        السعر (ريال):
                    </label>
                    <input type="number" 
                           name="amount" 
                           value="{{ old('amount', 250) }}"
                           required
                           style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        السرعة:
                    </label>
                    <select name="speed" 
                            required
                            style="width: 100%; padding: 12px; border: 2px solid #eee; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <option value="512k/512k" {{ old('speed') === '512k/512k' ? 'selected' : '' }}>
                            512k/512k — بطيء
                        </option>
                        <option value="256k/1M" {{ old('speed', '256k/1M') === '256k/1M' ? 'selected' : '' }}>
                            256k/1M — متوسط
                        </option>
                        <option value="512k/2M" {{ old('speed') === '512k/2M' ? 'selected' : '' }}>
                            512k/2M — عالي
                        </option>
                        <option value="2M/15M" {{ old('speed') === '2M/15M' ? 'selected' : '' }}>
                            2M/15M — مفتوح
                        </option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <x-button type="submit" style="flex: 1;">
                        💾 حفظ البطاقة
                    </x-button>
                    <x-button href="{{ route('cards.index') }}" variant="secondary">
                        إلغاء
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
