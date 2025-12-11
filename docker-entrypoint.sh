#!/bin/bash
set -e

echo "=== Starting Laravel Application ==="

# Ejecutar migraciones
echo "Running migrations..."
php artisan migrate --force || true

# Dar permisos
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

# Iniciar Apache
echo "Starting Apache..."
exec apache2-foreground
