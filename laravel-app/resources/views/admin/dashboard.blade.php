@extends('layouts.app')

@section('title', 'لوحة الإدارة')

@section('content')
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
        <h1 style="color: #c62828;">👤 لوحة الإدارة</h1>
        <span style="background: #c62828; color: white; padding: 5px 15px; border-radius: 20px; font-size: 13px; font-weight: bold;">
            مسؤول فقط
        </span>
    </div>
    
    {{-- Admin Stats --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 25px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 32px; margin-bottom: 10px;">🎫</div>
            <div style="font-size: 32px; font-weight: bold; color: #009639;">{{ $stats['total_cards'] }}</div>
            <div style="color: #666; font-size: 13px; margin-top: 5px;">إجمالي البطاقات</div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 32px; margin-bottom: 10px;">👥</div>
            <div style="font-size: 32px; font-weight: bold; color: #1565c0;">{{ $stats['total_users'] }}</div>
            <div style="color: #666; font-size: 13px; margin-top: 5px;">المستخدمون</div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 32px; margin-bottom: 10px;">💰</div>
            <div style="font-size: 32px; font-weight: bold; color: #c62828;">{{ number_format($stats['total_sales']) }}</div>
            <div style="color: #666; font-size: 13px; margin-top: 5px;">المبيعات (ريال)</div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 32px; margin-bottom: 10px;">📁</div>
            <div style="font-size: 32px; font-weight: bold; color: #f57c00;">{{ $stats['total_categories'] }}</div>
            <div style="color: #666; font-size: 13px; margin-top: 5px;">الفئات</div>
        </div>
    </div>
    
    {{-- Admin Sections --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
	<a href="{{ route('admin.users.index') }}" 
	   style="display: block; padding: 30px; background: white; border-radius: 12px; text-decoration: none; color: #333; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
	    <div style="font-size: 48px; margin-bottom: 15px;">👥</div>
	    <h3 style="color: #009639; margin-bottom: 10px;">إدارة المستخدمين</h3>
	    <p style="color: #666; font-size: 14px;">إضافة، تعديل، حذف المستخدمين</p>
	</a>

        <a href="{{ route('admin.settings') }}" 
           style="display: block; padding: 30px; background: white; border-radius: 12px; text-decoration: none; color: #333; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: all 0.2s;">
            <div style="font-size: 48px; margin-bottom: 15px;">⚙️</div>
            <h3 style="color: #009639; margin-bottom: 10px;">الإعدادات</h3>
            <p style="color: #666; font-size: 14px;">إعدادات النظام العامة</p>
        </a>
    </div>
@endsection
