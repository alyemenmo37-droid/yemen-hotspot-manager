<footer style="background: #333; color: white; padding: 30px 20px; margin-top: 40px;">
    <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
        <div style="font-size: 24px; margin-bottom: 10px;">📡 Yemen Hotspot Manager</div>
        
	<div style="opacity: 0.8; margin-bottom: 15px;">
	    v{{ config('app.version', '2.0.0') }} — Production Edition
	</div>

        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap; font-size: 13px; opacity: 0.7; margin-bottom: 15px;">
            <span>Laravel {{ app()->version() }}</span>
            <span>•</span>
            <span>PHP {{ PHP_VERSION }}</span>
            <span>•</span>
            <span>{{ now()->format('Y') }}</span>
        </div>
        
	<div style="text-align: center; margin-top: 15px; font-size: 12px;">
	    <a href="{{ route('health') }}" target="_blank" 
	       style="color: #00ff00; text-decoration: none;">
	        🟢 النظام يعمل
	    </a>
	</div>

        <div style="font-size: 13px; opacity: 0.6;">
            صُنع بحب في اليمن 🇾🇪
        </div>
    </div>
</footer>
