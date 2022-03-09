FROM composer:latest as composer

FROM php:7.3-cli

WORKDIR /app

RUN pecl install apcu && docker-php-ext-enable apcu && \
    echo "apc.enable_cli = 1" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer
ADD . /app

