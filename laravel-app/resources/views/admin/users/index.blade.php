@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h1 style="color: #009639;">👥 إدارة المستخدمين</h1>
        <a href="{{ route('admin.users.create') }}" 
           style="background: #009639; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
            ➕ إضافة مستخدم
        </a>
    </div>
    
    {{-- Stats --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div style="background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 28px; font-weight: bold; color: #009639;">{{ $stats['total'] }}</div>
            <div style="color: #666; font-size: 13px;">إجمالي</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 28px; font-weight: bold; color: #c62828;">{{ $stats['admins'] }}</div>
            <div style="color: #666; font-size: 13px;">مسؤولون</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 28px; font-weight: bold; color: #2e7d32;">{{ $stats['active'] }}</div>
            <div style="color: #666; font-size: 13px;">نشطون</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div style="font-size: 28px; font-weight: bold; color: #f57c00;">{{ $stats['inactive'] }}</div>
            <div style="color: #666; font-size: 13px;">موقوفون</div>
        </div>
    </div>
    
    {{-- Table --}}
    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 15px; text-align: right;">#</th>
                    <th style="padding: 15px; text-align: right;">الاسم</th>
                    <th style="padding: 15px; text-align: right;">البريد</th>
                    <th style="padding: 15px; text-align: right;">الدور</th>
                    <th style="padding: 15px; text-align: right;">الحالة</th>
                    <th style="padding: 15px; text-align: right;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 15px;">{{ $user->id }}</td>
                        <td style="padding: 15px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="width: 32px; height: 32px; background: #009639; color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                    {{ $user->initials() }}
                                </span>
                                <div>
                                    <div style="font-weight: bold;">{{ $user->name }}</div>
                                    @if($user->id === auth()->id())
                                        <span style="background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 10px; font-size: 10px;">أنت</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px; font-family: monospace; font-size: 13px;">{{ $user->email }}</td>
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
                        <td style="padding: 15px; white-space: nowrap;">
                            <a href="{{ route('admin.users.show', $user) }}" 
                               style="color: #009639; text-decoration: none; font-weight: bold;">عرض</a>
                            |
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               style="color: #f57c00; text-decoration: none; font-weight: bold;">تعديل</a>
                            
                            @if($user->id !== auth()->id())
                                |
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" style="background: none; border: none; color: {{ $user->is_active ? '#c62828' : '#2e7d32' }}; cursor: pointer; font-weight: bold; padding: 0;">
                                        {{ $user->is_active ? '⏸️ إيقاف' : '▶️ تفعيل' }}
                                    </button>
                                </form>
                                
                                |
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline"
                                      onsubmit="return confirm('حذف {{ $user->name }}؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #c62828; cursor: pointer; padding: 0;">
                                        🗑️
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 60px; text-align: center; color: #999;">
                            لا توجد مستخدمون
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 20px;">
        {{ $users->links() }}
    </div>
@endsection
