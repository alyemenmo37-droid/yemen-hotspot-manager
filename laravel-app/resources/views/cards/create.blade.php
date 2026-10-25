@extends('layouts.app')

@section('title', 'إضافة بطاقة')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: #009639; margin-bottom: 20px;">➕ إضافة بطاقة جديدة</h1>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <form method="POST" action="{{ route('cards.store') }}">
                @csrf
                
                {{-- رقم البطاقة --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        رقم البطاقة:
                    </label>
                    <input type="text" 
                           name="number" 
                           pattern="\d{10}" 
                           maxlength="10"
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
                
                {{-- نوع البطاقة (السرعة + المدة + السعر) --}}
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: bold;">
                        نوع البطاقة:
                    </label>
                    <select name="speed_level_id" 
                            required
                            style="width: 100%; padding: 12px; border: 2px solid {{ $errors->has('speed_level_id') ? '#c62828' : '#eee' }}; border-radius: 8px; font-size: 14px; box-sizing: border-box;">
                        <option value="">-- اختر نوع البطاقة --</option>
                        @foreach($speedLevels as $categoryName => $levels)
                            <optgroup label="📁 {{ $categoryName }}">
                                @foreach($levels as $level)
                                    <option value="{{ $level->id }}" 
                                        {{ old('speed_level_id') == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }} — {{ $level->rate_limit }} — {{ $level->price }} ريال — {{ $level->durationFormatted() }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('speed_level_id')
                        <div style="color: #c62828; font-size: 13px; margin-top: 5px;">
                            ❌ {{ $message }}
                        </div>
                    @enderror
                </div>
                
                {{-- ملاحظة --}}
                <div style="margin-bottom: 20px; padding: 12px; background: #e8f5e9; border-radius: 8px; color: #2e7d32; font-size: 13px;">
                    💡 <strong>السعر والسرعة يُحدَّدان تلقائياً</strong> بناءً على النوع المختار
                </div>
                
                {{-- الأزرار --}}
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
