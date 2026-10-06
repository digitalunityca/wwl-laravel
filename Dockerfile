FROM php:8.4-apache-bookworm
ARG WWWUSER=501
RUN apt-get update && apt-get install -y --no-install-recommends git unzip libzip-dev libicu-dev \
    && docker-php-ext-install -j$(nproc) intl zip pcntl bcmath \
    && a2enmod rewrite \
    && usermod -u ${WWWUSER} www-data \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
WORKDIR /var/www/html
