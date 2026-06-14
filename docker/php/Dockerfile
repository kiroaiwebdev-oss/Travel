# PHP 8.4 FPM image for Laravel 12
FROM php:8.4-fpm-alpine

# System deps
RUN apk add --no-cache \
        bash git curl libpng-dev libjpeg-turbo-dev freetype-dev \
        libzip-dev oniguruma-dev icu-dev $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP deps first for better layer caching (run on VPS where internet is available)
COPY backend/composer.json backend/composer.lock* ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist || true

COPY backend/ ./

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 9000
CMD ["php-fpm"]
