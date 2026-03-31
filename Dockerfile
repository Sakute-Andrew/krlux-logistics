FROM php:8.3-fpm

# Встановлення системних залежностей
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    nginx

# PHP розширення
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd intl zip

# Копіюємо проєкт
COPY . /var/www
WORKDIR /var/www

# Встановлення Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Встановлюємо залежності
RUN composer install --no-dev --optimize-autoloader

# Фікс прав (враховуючи твою минулу помилку)
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Копіюємо конфіг Nginx
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

EXPOSE 80

RUN echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/www.conf

# Використовуємо повний шлях до php-fpm, який є стандартом для образу 8.3-fpm
CMD ["sh", "-c", "php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan migrate:fresh --seed --force && php artisan filament:assets && /usr/local/sbin/php-fpm -D && nginx -g 'daemon off;'"]
