@extends('layouts.app')

@section('title', "بطاقة {$card->number}")

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <h1 style="color: #009639; margin-bottom: 20px;">
            📇 البطاقة: {{ $card->number }}
        </h1>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <table style="width: 100%;">
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">الرقم:</td>
                    <td style="padding: 12px 0; font-family: monospace; font-weight: bold;">
                        {{ $card->number }}
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">السعر:</td>
                    <td style="padding: 12px 0;">{{ $card->amount }} ريال</td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">السرعة:</td>
                    <td style="padding: 12px 0;">
                        <span style="background: #e3f2fd; color: #1565c0; padding: 4px 12px; border-radius: 12px;">
                            {{ $card->speed }}
                        </span>
                    </td>
                </tr>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">الحالة:</td>
                    <td style="padding: 12px 0;">
                        <x-card-status :status="$card->status" />
                    </td>
                </tr>
                @if($card->data_limit_mb)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 0; color: #666; font-weight: bold;">البيانات:</td>
                        <td style="padding: 12px 0;">
                            {{ $card->data_used_mb }} / {{ $card->data_limit_mb }} MB
                            ({{ $card->dataPercentage() }}%)
                        </td>
                    </tr>
                @endif
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px 0; color: #666; font-weight: bold;">تاريخ الإنشاء:</td>
                    <td style="padding: 12px 0;">{{ $card->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                @if($card->activated_at)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px 0; color: #666; font-weight: bold;">تاريخ التفعيل:</td>
                        <td style="padding: 12px 0;">{{ $card->activated_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @endif
            </table>
        </div>
        
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <x-button href="{{ route('cards.edit', $card) }}">
                ✏️ تعديل
            </x-button>
            <x-button href="{{ route('cards.index') }}" variant="secondary">
                ← رجوع
            </x-button>
        </div>
    </div>
@endsection
