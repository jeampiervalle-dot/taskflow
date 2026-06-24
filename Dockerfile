FROM serversideup/php:8.3-fpm-nginx AS build

ENV PHP_OPCACHE_ENABLE=1

USER root

RUN apt-get update && apt-get install -y \
    nodejs \
    npm \
    libmongoc-1.0-0 \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY --chown=www-data:www-data . /var/www/html

USER www-data

RUN npm install && npm run build \
    && composer install --no-interaction --optimize-autoloader --no-dev

RUN php artisan storage:link || true

EXPOSE 80
