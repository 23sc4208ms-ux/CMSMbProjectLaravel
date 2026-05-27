FROM php:8.4-cli-bookworm AS php-base

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring zip intl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json ./
RUN npm install

COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

FROM php-base AS vendor

COPY composer.json composer.lock ./
COPY artisan bootstrap/ ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress --no-scripts

FROM php-base AS app

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY docker/start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Allow composer as superuser during app-stage post-install steps
ENV COMPOSER_ALLOW_SUPERUSER=1

# Ensure autoload & package discovery run after vendor and app files are present
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-progress || true \
    && composer dump-autoload --optimize --no-interaction --classmap-authoritative || true \
    && php artisan package:discover --ansi || true

EXPOSE 10000

# Use startup script that runs migrations then launches the server
CMD ["/usr/local/bin/start.sh"]

