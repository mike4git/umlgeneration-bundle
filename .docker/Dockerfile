
FROM mlocati/php-extension-installer AS php-extension-installer
FROM composer AS composer
FROM php:8.1.7-apache

COPY --from=php-extension-installer /usr/bin/install-php-extensions /usr/bin/
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN set -eux; \
    mkdir /app; \
    install-php-extensions xdebug;

COPY php.ini /usr/local/etc/php/

WORKDIR /app
