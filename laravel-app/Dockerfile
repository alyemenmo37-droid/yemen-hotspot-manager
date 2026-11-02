# ═══════════════════════════════════════
# YHM - Dockerfile (Standalone)
# لا يحتاج مجلد docker/
# ═══════════════════════════════════════

FROM php:8.3-fpm-alpine

# ═══════════════════════════════════════
# 1. تثبيت الحزم الأساسية
# ═══════════════════════════════════════
RUN apk add --no-cache \
    netcat-openbsd \
    curl \
    nginx \
    supervisor \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    mysql-client \
    bash \
    nodejs \
    npm

# ═══════════════════════════════════════
# 2. تثبيت إضافات PHP
# ═══════════════════════════════════════
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    bcmath \
    opcache \
    intl

# ═══════════════════════════════════════
# 3. إعدادات PHP
# ═══════════════════════════════════════
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini

# ═══════════════════════════════════════
# 4. مجلد العمل
# ═══════════════════════════════════════
WORKDIR /var/www/html

# ═══════════════════════════════════════
# 5. Composer
# ═══════════════════════════════════════
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ═══════════════════════════════════════
# 6. نسخ ملفات المشروع
# ═══════════════════════════════════════
COPY . .

# ═══════════════════════════════════════
# 7. تثبيت الاعتماديات
# ═══════════════════════════════════════
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# ═══════════════════════════════════════
# 8. إنشاء المجلدات
# ═══════════════════════════════════════
RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/backups \
    bootstrap/cache \
    /etc/supervisor/conf.d \
    /var/log/supervisor

# ═══════════════════════════════════════
# 9. الصلاحيات
# ═══════════════════════════════════════
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# ═══════════════════════════════════════
# 10. Nginx Config (مدمج)
# ═══════════════════════════════════════
RUN echo 'worker_processes auto;' > /etc/nginx/nginx.conf && \
    echo 'error_log /var/log/nginx/error.log warn;' >> /etc/nginx/nginx.conf && \
    echo 'pid /var/run/nginx.pid;' >> /etc/nginx/nginx.conf && \
    echo 'events { worker_connections 1024; }' >> /etc/nginx/nginx.conf && \
    echo 'http {' >> /etc/nginx/nginx.conf && \
    echo '    include /etc/nginx/mime.types;' >> /etc/nginx/nginx.conf && \
    echo '    default_type application/octet-stream;' >> /etc/nginx/nginx.conf && \
    echo '    sendfile on;' >> /etc/nginx/nginx.conf && \
    echo '    keepalive_timeout 65;' >> /etc/nginx/nginx.conf && \
    echo '    client_max_body_size 20M;' >> /etc/nginx/nginx.conf && \
    echo '    server {' >> /etc/nginx/nginx.conf && \
    echo '        listen 80;' >> /etc/nginx/nginx.conf && \
    echo '        server_name _;' >> /etc/nginx/nginx.conf && \
    echo '        root /var/www/html/public;' >> /etc/nginx/nginx.conf && \
    echo '        index index.php;' >> /etc/nginx/nginx.conf && \
    echo '        charset utf-8;' >> /etc/nginx/nginx.conf && \
    echo '        location / { try_files $uri $uri/ /index.php?$query_string; }' >> /etc/nginx/nginx.conf && \
    echo '        location ~ \.php$ {' >> /etc/nginx/nginx.conf && \
    echo '            fastcgi_pass 127.0.0.1:9000;' >> /etc/nginx/nginx.conf && \
    echo '            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;' >> /etc/nginx/nginx.conf && \
    echo '            include fastcgi_params;' >> /etc/nginx/nginx.conf && \
    echo '        }' >> /etc/nginx/nginx.conf && \
    echo '        location ~ /\.(?!well-known) { deny all; }' >> /etc/nginx/nginx.conf && \
    echo '    }' >> /etc/nginx/nginx.conf && \
    echo '}' >> /etc/nginx/nginx.conf

# ═══════════════════════════════════════
# 11. Supervisor Config (مدمج)
# ═══════════════════════════════════════
RUN echo '[supervisord]' > /etc/supervisor/conf.d/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'logfile=/var/log/supervisor/supervisord.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'pidfile=/var/run/supervisord.pid' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:php-fpm]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=php-fpm' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile=/var/log/supervisor/php-fpm.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile=/var/log/supervisor/php-fpm.err.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:nginx]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=nginx -g "daemon off;"' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile=/var/log/supervisor/nginx.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile=/var/log/supervisor/nginx.err.log' >> /etc/supervisor/conf.d/supervisord.conf

# ═══════════════════════════════════════
# 12. Entrypoint Script (مدمج)
# ═══════════════════════════════════════
RUN echo '#!/bin/sh' > /usr/local/bin/entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "🚀 Starting YHM..."' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "⏳ Waiting for database..."' >> /usr/local/bin/entrypoint.sh && \
    echo 'until nc -z db 3306; do sleep 2; done' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "✅ Database ready"' >> /usr/local/bin/entrypoint.sh && \
    echo 'if [ ! -f /var/www/html/storage/.migrated ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    echo "📊 Running migrations..."' >> /usr/local/bin/entrypoint.sh && \
    echo '    php artisan migrate --force' >> /usr/local/bin/entrypoint.sh && \
    echo '    php artisan db:seed --class=UserSeeder --force || true' >> /usr/local/bin/entrypoint.sh && \
    echo '    php artisan db:seed --class=CategorySpeedSeeder --force || true' >> /usr/local/bin/entrypoint.sh && \
    echo '    php artisan db:seed --class=SettingSeeder --force || true' >> /usr/local/bin/entrypoint.sh && \
    echo '    touch /var/www/html/storage/.migrated' >> /usr/local/bin/entrypoint.sh && \
    echo '    echo "✅ Migrations complete"' >> /usr/local/bin/entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/entrypoint.sh && \
    echo 'if [ -z "$APP_KEY" ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    echo "🔑 Generating app key..."' >> /usr/local/bin/entrypoint.sh && \
    echo '    php artisan key:generate --force' >> /usr/local/bin/entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "⚡ Optimizing..."' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan config:cache || true' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan route:cache || true' >> /usr/local/bin/entrypoint.sh && \
    echo 'php artisan view:cache || true' >> /usr/local/bin/entrypoint.sh && \
    echo 'if [ ! -L /var/www/html/public/storage ]; then' >> /usr/local/bin/entrypoint.sh && \
    echo '    php artisan storage:link || true' >> /usr/local/bin/entrypoint.sh && \
    echo 'fi' >> /usr/local/bin/entrypoint.sh && \
    echo 'echo "✅ YHM ready!"' >> /usr/local/bin/entrypoint.sh && \
    echo 'exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf' >> /usr/local/bin/entrypoint.sh && \
    chmod +x /usr/local/bin/entrypoint.sh

# ═══════════════════════════════════════
# 13. المنفذ
# ═══════════════════════════════════════
EXPOSE 80

# ═══════════════════════════════════════
# 14. الأمر النهائي
# ═══════════════════════════════════════
CMD ["/usr/local/bin/entrypoint.sh"]
