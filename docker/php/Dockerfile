ARG PHP_VERSION=""
ARG COMPOSER_MEMORY_LIMIT=-1

FROM php:${PHP_VERSION:+${PHP_VERSION}-}fpm-alpine

RUN apk update \
    && apk upgrade \
    && apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS

# CTYPE
RUN docker-php-ext-install -j$(nproc) ctype \
    && docker-php-ext-enable ctype \
    && apk add php-ctype

# ZIP
RUN apk add libzip-dev \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-enable zip \
    && apk add php-zip

# GD
RUN apk add libgd libpng-dev \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-enable gd \
    && apk add php-gd

# INTL
RUN apk add icu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-enable intl \
    && apk add php-intl

# INSTALL REQUIRED PACKAGES
RUN apk add php-dom
#RUN apk add php-iconv
RUN apk add php-session
RUN apk add php-simplexml
RUN apk add php-tokenizer
RUN apk add php-xml
RUN apk add php-xmlwriter

# Composer PHP manager
# DO NOT USE RUN "apk add composer" as it forces its own PHP version (irrelevant to one requested in FROM!)
RUN php -r "copy('https://getcomposer.org/download/2.2.18/composer.phar', 'composer.phar');" \
    && php -r "if (hash_file('sha256', 'composer.phar') === '28a8d9740d615137a8c01d32aef9184db16f543fca36db038501a294d8e95b24') { echo 'Binary verified'; } else { echo 'Binary corrupt'; unlink('composer.phar'); } echo PHP_EOL;" \
    && cp composer.phar /usr/bin/composer \
    && chmod +x /usr/bin/composer

RUN apk del .phpize-deps
