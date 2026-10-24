<header>
    <div class="header-content">
        <a href="{{ route('home') }}" class="logo">
            📡 Yemen Hotspot Manager
        </a>
        
        <nav class="nav">
            <a href="{{ route('home') }}" 
               class="{{ request()->routeIs('home') ? 'active' : '' }}">
                🏠 الرئيسية
            </a>
            <a href="{{ route('yhm') }}" 
               class="{{ request()->routeIs('yhm') ? 'active' : '' }}">
                📊 لوحة التحكم
            </a>
            <a href="{{ route('cards.index') }}" 
               class="{{ request()->routeIs('cards.*') ? 'active' : '' }}">
                🎫 البطاقات
            </a>
            <a href="{{ route('admin.dashboard') }}" 
               class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                👤 الإدارة
            </a>
        </nav>
    </div>
</header>
