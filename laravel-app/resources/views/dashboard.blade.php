@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
    <h1 style="color: #009639; margin-bottom: 20px;">📊 لوحة التحكم</h1>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
	<x-stat-card 
	    title="إجمالي البطاقات" :value="$stats['total']" icon="🎫" color="#009639" />
	<x-stat-card 
	    title="نشطة" :value="$stats['active']" icon="✅" color="#2e7d32" />
	<x-stat-card 
	    title="غير مستخدمة" :value="$stats['unused']" icon="⏸️" color="#f57c00" />
	<x-stat-card 
	    title="المبيعات" :value="number_format($stats['sales']) . ' ريال'" icon="💰" color="#c62828" />
    </div>
    
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
        <h3 style="color: #009639; margin-bottom: 15px;">🚀 البدء السريع</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <a href="{{ route('cards.index') }}" 
               style="display: block; padding: 20px; background: #f9f9f9; border-radius: 10px; text-decoration: none; color: #333; transition: all 0.2s;"
               onmouseover="this.style.background='#e8f5e9'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='#f9f9f9'; this.style.transform='translateY(0)'">
                <div style="font-size: 32px; margin-bottom: 10px;">🎫</div>
                <div style="font-weight: bold; margin-bottom: 5px;">إدارة البطاقات</div>
                <div style="color: #666; font-size: 13px;">عرض، إضافة، تعديل البطاقات</div>
            </a>
            
            <a href="{{ route('cards.create') }}" 
               style="display: block; padding: 20px; background: #f9f9f9; border-radius: 10px; text-decoration: none; color: #333; transition: all 0.2s;"
               onmouseover="this.style.background='#fff3e0'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='#f9f9f9'; this.style.transform='translateY(0)'">
                <div style="font-size: 32px; margin-bottom: 10px;">➕</div>
                <div style="font-weight: bold; margin-bottom: 5px;">إضافة بطاقة</div>
                <div style="color: #666; font-size: 13px;">إنشاء بطاقة جديدة</div>
            </a>
            
            <a href="{{ route('admin.dashboard') }}" 
               style="display: block; padding: 20px; background: #f9f9f9; border-radius: 10px; text-decoration: none; color: #333; transition: all 0.2s;"
               onmouseover="this.style.background='#e3f2fd'; this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='#f9f9f9'; this.style.transform='translateY(0)'">
                <div style="font-size: 32px; margin-bottom: 10px;">👤</div>
                <div style="font-weight: bold; margin-bottom: 5px;">الإدارة</div>
                <div style="color: #666; font-size: 13px;">لوحة الإدارة الرئيسية</div>
            </a>
        </div>
    </div>
@endsection
