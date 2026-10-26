<header>
    <div class="header-content">
        <a href="{{ route('dashboard') }}" class="logo">
            📡 Yemen Hotspot Manager
        </a>
        
        @auth
            <nav class="nav">
                <a href="{{ route('dashboard') }}" 
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    📊 لوحة التحكم
                </a>
                <a href="{{ route('cards.index') }}" 
                   class="{{ request()->routeIs('cards.*') ? 'active' : '' }}">
                    🎫 البطاقات
                </a>
		@if(auth()->user()->isAdmin())
		    <a href="{{ route('admin.dashboard') }}" 
		       class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
		        👤 الإدارة
		    </a>
		@endif
                
                {{-- User menu --}}
		<div style="display: flex; align-items: center; gap: 10px; margin-right: auto;">
		    <a href="{{ route('profile.show') }}" 
		       style="display: flex; align-items: center; gap: 5px; text-decoration: none; color: white;">
		        <span style="width: 32px; height: 32px; background: white; color: #009639; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
		            {{ auth()->user()->initials() }}
		        </span>
		        <span style="font-size: 14px;">{{ auth()->user()->name }}</span>
		    </a>
		    
		    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
		        @csrf
		        <button type="submit" style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px;">
		            🚪 خروج
		        </button>
		    </form>
		</div>
            </nav>
        @endauth
        
        @guest
            <nav class="nav">
                <a href="{{ route('login') }}">🔐 تسجيل الدخول</a>
            </nav>
        @endguest
    </div>
</header>
