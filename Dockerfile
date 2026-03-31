FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    zip unzip git curl nginx nodejs gettext-base npm

RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd intl zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY . /var/www
WORKDIR /var/www

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www

COPY ./docker/nginx.conf /etc/nginx/sites-available/default

RUN echo "listen = 127.0.0.1:9000" >> /usr/local/etc/php-fpm.d/www.conf

EXPOSE 80

CMD ["sh", "-c", "\
    php artisan config:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    php artisan filament:assets && \
    /usr/local/sbin/php-fpm -D && \
    envsubst '${PORT}' < /etc/nginx/sites-available/default > /etc/nginx/sites-enabled/default && \
    nginx -g 'daemon off;'"]
