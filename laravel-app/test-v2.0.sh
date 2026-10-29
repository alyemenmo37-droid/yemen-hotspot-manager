#!/bin/bash
# ═══════════════════════════════════════
# YHM v2.0 - Final Test Suite
# ═══════════════════════════════════════

echo "🧪 YHM v2.0 Test Suite"
echo "════════════════════════════════════════"

# 1. Health Check
echo ""
echo "🏥 1. Health Check"
php artisan health:check || exit 1

# 2. Database
echo ""
echo "🗄️  2. Database"
TABLE_COUNT=$(mariadb -u yhm_user -pYHM_pass_2026 yhm_laravel -e "SHOW TABLES;" 2>/dev/null | tail -n +2 | wc -l)
echo "  Tables: $TABLE_COUNT"

CARD_COUNT=$(mariadb -u yhm_user -pYHM_pass_2026 yhm_laravel -e "SELECT COUNT(*) FROM cards;" 2>/dev/null | tail -1)
echo "  Cards: $CARD_COUNT"

RADIUS_COUNT=$(mariadb -u yhm_user -pYHM_pass_2026 yhm_laravel -e "SELECT COUNT(*) FROM radcheck;" 2>/dev/null | tail -1)
echo "  RADIUS: $RADIUS_COUNT"

# 3. Routes
echo ""
echo "🛣️  3. Routes"
ROUTE_COUNT=$(php artisan route:list 2>/dev/null | grep -cE "GET|POST|PUT|DELETE")
echo "  Total: $ROUTE_COUNT"

# 4. Backup
echo ""
echo "💾 4. Backup"
php artisan backup:database --compress 2>&1 | grep "تم النسخ" || echo "  ⚠️ Backup failed"

# 5. Security
echo ""
echo "🔒 5. Security"
composer audit 2>&1 | grep -E "No security|Found" | head -1

# 6. Public Routes
echo ""
echo "🌐 6. Public Routes"
BASE_URL="http://127.0.0.1:8000"
for route in "/" "/login" "/about" "/health"; do
    code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL$route" 2>/dev/null)
    echo "  $route → $code"
done

# 7. Protected Routes (should redirect 302)
echo ""
echo "🔐 7. Protected Routes (302 expected)"
for route in "/dashboard" "/cards" "/statistics" "/mikrotik" "/admin"; do
    code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL$route" 2>/dev/null)
    echo "  $route → $code"
done

echo ""
echo "════════════════════════════════════════"
echo "✅ Test complete!"

# 8. Summary
echo ""
echo "📊 Summary:"
echo "  Version: 2.0.0"
echo "  Laravel: $(php artisan --version | cut -d' ' -f3)"
echo "  PHP: $(php -r 'echo phpversion();')"
echo "  Date: $(date '+%Y-%m-%d %H:%M:%S')"
