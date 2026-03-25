FROM php:8.4-fpm-alpine

RUN apk add --no-cache mysql-dev linux-headers \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-enable redis \
    && apk del .build-deps

# Копируем php.ini для development
RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

# Копируем кастомные конфигурации PHP (limit.ini, security.ini, opcache.ini)
COPY ./common/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /app