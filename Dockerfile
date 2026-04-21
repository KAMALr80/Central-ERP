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

# ==================== REDIS ====================
RUN pecl install redis && docker-php-ext-enable redis || true

# ==================== COMPOSER ====================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ==================== WORKDIR ====================
WORKDIR /var/www/html

# ==================== COPY APP ====================
COPY . .

# ==================== INSTALL ====================
RUN composer install --optimize-autoloader --no-interaction --no-dev

# ==================== APACHE CONFIG ====================
# Ensure we have a default config or create one if missing
RUN if [ ! -f public/000-default.conf ]; then \
    echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf; \
    else \
    cp public/000-default.conf /etc/apache2/sites-available/000-default.conf; \
    fi

RUN a2enmod rewrite headers

# ==================== PERMISSIONS ====================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# ==================== ENTRYPOINT ====================
RUN chmod +x docker-entrypoint.sh
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]

# Render uses dynamic port, but we default to 80 internally
EXPOSE 80