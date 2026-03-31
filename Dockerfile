FROM php:8.3

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

# Дозволяємо Composer працювати під root (для Docker білду)
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader

# Налаштування прав
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Копіюємо конфіг Nginx
COPY ./docker/nginx.conf /etc/nginx/sites-available/default

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
