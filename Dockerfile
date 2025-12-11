# Usamos la imagen oficial de PHP con Apache
FROM php:8.3-apache

# 1. Instalar dependencias del sistema (Linux) necesarias para GD y otras utilidades
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 2. Configurar e instalar extensiones de PHP (AQUÍ SE SOLUCIONA TU ERROR)
# La extensión GD necesita configuración previa para soportar jpeg y freetype
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql zip bcmath

# 3. Habilitar mod_rewrite de Apache (Necesario para las rutas de Laravel)
RUN a2enmod rewrite

# 4. Configurar el "Document Root" de Apache
# Laravel sirve los archivos desde la carpeta /public, no desde la raíz
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. Instalar Composer dentro del contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Establecer directorio de trabajo
WORKDIR /var/www/html

# 7. Copiar todos los archivos de tu proyecto al contenedor
COPY . .

# 8. Instalar dependencias de Composer
# --no-dev: Para no instalar cosas de prueba en producción
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 9. Dar permisos a las carpetas de almacenamiento y caché de Laravel
# Si no haces esto, te saldrá un "Error 500" después
RUN chown -R www-data:www-data storage bootstrap/cache

# 10. Exponer el puerto 80 (El puerto estándar de la web)
EXPOSE 80