<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'YHM') - {{ config('app.name') }}</title>
    
    {{-- CSS --}}
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Header */
        header {
            background: linear-gradient(135deg, #009639, #007a2e);
            color: white;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }
        
        .nav {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        
        .nav a:hover,
        .nav a.active {
            background: rgba(255,255,255,0.2);
        }
        
        /* Main */
        main {
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-right: 4px solid #2e7d32;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-right: 4px solid #c62828;
        }
        
        .alert-warning {
            background: #fff3e0;
            color: #e65100;
            border-right: 4px solid #e65100;
        }
        
        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border-right: 4px solid #1565c0;
        }
        
        /* Footer */
        footer {
            background: #333;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
        
        /* Buttons */
        .btn {
            background: #009639;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }
        
        .btn:hover { background: #007a2e; }
        
        .btn-secondary { background: #666; }
        .btn-secondary:hover { background: #444; }
        
        .btn-danger { background: #c62828; }
        .btn-danger:hover { background: #8b0000; }
    </style>
    
    @stack('styles')
</head>
<body>
    {{-- Header --}}
    @include('partials.header')
    
    {{-- Main Content --}}
    <main>
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">
                ❌ {{ session('error') }}
            </div>
        @endif
        
        @if(session('warning'))
            <div class="alert alert-warning">
                ⚠️ {{ session('warning') }}
            </div>
        @endif
        
        @if(session('info'))
            <div class="alert alert-info">
                ℹ️ {{ session('info') }}
            </div>
        @endif
        
        {{-- Page Content --}}
        @yield('content')
    </main>
    
    {{-- Footer --}}
    @include('partials.footer')
    
    {{-- Scripts --}}
    @stack('scripts')
{{-- Toast Container --}}
<div id="toast-container" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>

<style>
.toast {
    padding: 15px 25px;
    border-radius: 10px;
    color: white;
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    animation: slideDown 0.3s ease;
    min-width: 250px;
}

.toast-success { background: #2e7d32; }
.toast-error { background: #c62828; }
.toast-info { background: #1565c0; }

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
function showToast(message, type = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>
</body>
</html>
