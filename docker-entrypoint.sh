#!/bin/bash
set -e

echo "=== Starting Laravel Application ==="

# Limpiar cache completamente
echo "Clearing all caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true
php artisan event:clear || true

# Verificar configuración de mail antes de cachear
echo "Mail configuration check:"
echo "MAIL_ENABLED=${MAIL_ENABLED}"
echo "MAIL_HOST=${MAIL_HOST}"
echo "MAIL_MAILER=${MAIL_MAILER}"

# Reconstruir cache con las variables de entorno actuales
echo "Building cache..."
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones
echo "Running migrations..."
php artisan migrate --force || true

# Dar permisos
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Iniciar Apache
echo "Starting Apache..."
exec apache2-foreground
