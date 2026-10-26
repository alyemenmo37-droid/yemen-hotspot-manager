@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h1 style="color: #009639;">👥 إدارة المستخدمين</h1>
        <a href="{{ route('admin.dashboard') }}" 
           style="color: #666; text-decoration: none;">
            ← العودة
        </a>
    </div>
    
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 700px;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 15px; text-align: right;">#</th>
                    <th style="padding: 15px; text-align: right;">الاسم</th>
                    <th style="padding: 15px; text-align: right;">البريد</th>
                    <th style="padding: 15px; text-align: right;">الدور</th>
                    <th style="padding: 15px; text-align: right;">الحالة</th>
                    <th style="padding: 15px; text-align: right;">آخر دخول</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;">{{ $user->id }}</td>
                        <td style="padding: 15px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="width: 32px; height: 32px; background: #009639; color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                    {{ $user->initials() }}
                                </span>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td style="padding: 15px; font-family: monospace; font-size: 13px;">
                            {{ $user->email }}
                        </td>
                        <td style="padding: 15px;">
                            @php
                                $roleColors = [
                                    'admin' => ['bg' => '#ffebee', 'color' => '#c62828', 'label' => '👑 مسؤول'],
                                    'manager' => ['bg' => '#fff3e0', 'color' => '#e65100', 'label' => '👔 مشرف'],
                                    'operator' => ['bg' => '#e3f2fd', 'color' => '#1565c0', 'label' => '🔧 مشغل'],
                                ];
                                $rc = $roleColors[$user->role] ?? ['bg' => '#eee', 'color' => '#666', 'label' => $user->role];
                            @endphp
                            <span style="background: {{ $rc['bg'] }}; color: {{ $rc['color'] }}; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;">
                                {{ $rc['label'] }}
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            @if($user->is_active)
                                <span style="color: #2e7d32;">✅ نشط</span>
                            @else
                                <span style="color: #c62828;">❌ موقوف</span>
                            @endif
                        </td>
                        <td style="padding: 15px; font-size: 12px; color: #666;">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'لم يدخل بعد' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
