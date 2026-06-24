FROM serversideup/php:8.3-fpm-nginx AS build

ENV PHP_OPCACHE_ENABLE=1

USER root

RUN apt-get update && apt-get install -y \
    nodejs \
    npm \
    libmongoc-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY --chown=www-data:www-data . /var/www/html

USER www-data

RUN npm install && npm run build \
    && composer install --no-interaction --optimize-autoloader --no-dev

USER root
RUN mkdir -p /var/www/html/storage/framework/{sessions,views,cache,testing} \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/app/public \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

USER www-data

RUN php artisan storage:link || true

EXPOSE 80
