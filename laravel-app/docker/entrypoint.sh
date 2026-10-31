#!/bin/sh
set -e

echo "🚀 Starting YHM..."

# Wait for database
echo "⏳ Waiting for database..."
until php artisan db:show > /dev/null 2>&1; do
    sleep 2
done
echo "✅ Database ready"

# Run migrations (only if not cached)
if [ ! -f /var/www/html/storage/.migrated ]; then
    echo "📊 Running migrations..."
    php artisan migrate --force
    php artisan db:seed --class=UserSeeder --force
    php artisan db:seed --class=CategorySpeedSeeder --force
    php artisan db:seed --class=SettingSeeder --force
    touch /var/www/html/storage/.migrated
    echo "✅ Migrations complete"
fi

# Generate key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating app key..."
    php artisan key:generate --force
fi

# Cache configuration
echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
fi

echo "✅ YHM ready!"
echo "🌐 Starting services..."

# Start supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
