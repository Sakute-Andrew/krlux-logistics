FROM php:8.3-fpm

# Встановлення системних залежностей
RUN apt-get update && apt-get install -y \
    libpng-dev \
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
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

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

# Використовуємо повний шлях до php-fpm, який є стандартом для образу 8.3-fpm
CMD ["sh", "-c", "/usr/local/sbin/php-fpm -D && nginx -g 'daemon off;'"]
