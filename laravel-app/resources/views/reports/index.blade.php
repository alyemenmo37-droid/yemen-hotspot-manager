@extends('layouts.app')

@section('title', 'التقارير')

@section('content')
    <h1 style="color: #009639; margin-bottom: 25px;">📄 التقارير</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        
        {{-- تقرير البطاقات --}}
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 48px; margin-bottom: 15px;">🎫</div>
            <h3 style="color: #009639; margin-bottom: 10px;">تقرير البطاقات</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
                قائمة كل البطاقات مع التفاصيل الكاملة
            </p>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('reports.cards.pdf') }}" 
                   style="background: #c62828; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">
                    📄 PDF
                </a>
                <a href="{{ route('reports.cards.csv') }}" 
                   style="background: #2e7d32; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">
                    📊 Excel
                </a>
            </div>
        </div>
        
        {{-- تقرير المبيعات --}}
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 48px; margin-bottom: 15px;">💰</div>
            <h3 style="color: #009639; margin-bottom: 10px;">تقرير المبيعات</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
                المبيعات حسب الفئة وآخر 30 يوم
            </p>
            <a href="{{ route('reports.sales.pdf') }}" 
               style="background: #c62828; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">
                📄 PDF
            </a>
        </div>
        
        {{-- تقرير الاستهلاك --}}
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 48px; margin-bottom: 15px;">📊</div>
            <h3 style="color: #009639; margin-bottom: 10px;">تقرير الاستهلاك</h3>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">
                أعلى 30 مستهلكاً للبيانات
            </p>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('reports.usage.pdf') }}" 
                   style="background: #c62828; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">
                    📄 PDF
                </a>
                <a href="{{ route('reports.usage.csv') }}" 
                   style="background: #2e7d32; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px;">
                    📊 Excel
                </a>
            </div>
        </div>
    </div>
@endsection
