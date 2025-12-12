# Usar PHP 8.3 con Apache
FROM php:8.3-apache

# Copiar composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Deshabilitar MPMs conflictivos y habilitar solo prefork
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip bcmath opcache

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Composer
# Actualizar solo el paquete nuevo y luego instalar
RUN composer update symfony/brevo-mailer --no-interaction --ignore-platform-req=ext-zip || true
RUN composer install --optimize-autoloader --no-dev --no-interaction --ignore-platform-req=ext-zip

# Instalar dependencias de NPM y compilar assets
RUN npm ci && npm run build

# NO cachear configuración aquí - se hará en entrypoint con las variables de entorno correctas
# El cache se genera en docker-entrypoint.sh cuando las variables estén disponibles

# Dar permisos a las carpetas necesarias
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copiar script de inicio
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Exponer puerto
EXPOSE 80

# Comando de inicio
ENTRYPOINT ["docker-entrypoint.sh"]
