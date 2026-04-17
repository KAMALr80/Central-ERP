FROM php:8.3-apache

# ==================== SYSTEM DEPENDENCIES ====================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libwebp-dev \
    libxpm-dev \
    libssl-dev \
    libgd-dev \
    nano \
    vim \
    && rm -rf /var/lib/apt/lists/*

# ==================== PHP EXTENSIONS ====================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp

RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mysqli \
    zip \
    gd \
    exif \
    pcntl \
    bcmath \
    opcache \
    sockets

RUN docker-php-ext-enable gd

# ==================== REDIS ====================
RUN pecl install redis && docker-php-ext-enable redis || true

# ==================== APACHE CONFIG ====================
COPY public/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite \
    && a2enmod headers \
    && a2enmod ssl \
    && a2enmod deflate

# ==================== COMPOSER ====================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==================== WORKDIR ====================
WORKDIR /var/www/html

# ==================== COPY APP ====================
COPY . .

# ==================== INSTALL ====================
RUN composer install --optimize-autoloader --no-interaction

# ==================== PERMISSIONS ====================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/public

# ==================== PHP CONFIG ====================
RUN echo "upload_max_filesize = 100M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_input_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "gd.jpeg_ignore_warning = 1" >> /usr/local/etc/php/conf.d/gd.ini

# ==================== STORAGE ====================
RUN php artisan storage:link || true

# ==================== SAFE BUILD ====================
RUN php artisan config:clear || true

# ==================== PORT FIX ====================
EXPOSE 10000

# ==================== HEALTHCHECK ====================
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD curl -f http://localhost:10000/ || exit 1

# ==================== START ====================
CMD export CACHE_DRIVER=file && \
    export PORT=10000 && \
    sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf && \
    sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf && \
    php artisan config:clear && \
    php artisan config:cache && \
    php artisan migrate --force && \
    apache2-foreground