#!/bin/bash
set -e

# Load environment variables
echo "🚀 Starting Enterprise ERP Deployment Sequence..."

# Sync dynamic port for Render
if [ -n "$PORT" ]; then
    echo "⚙️ Configuring Apache to listen on port $PORT"
    sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
    sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf
fi

# Optimization for Production
echo "🧹 Clearing and caching configurations..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Optimizing for Production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Run Migrations
echo "📦 Running Central Database Migrations..."
php artisan migrate --force

echo "🏢 Running Tenant Databases Migrations..."
php artisan tenants:migrate --force

# Link Storage
echo "🔗 Linking storage..."
php artisan storage:link --force || true

echo "✅ System is Ready! Launching Apache..."
exec apache2-foreground
