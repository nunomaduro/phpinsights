FROM php:7.4-cli-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:1.10 /usr/bin/composer /usr/bin/composer

COPY . /phpinsights

RUN apk add --no-cache --update git \
    \
    && composer install -d /phpinsights \
    --no-dev \
    --no-ansi \
    --no-interaction \
    --no-scripts \
    --no-suggest \
    --no-progress \
    --prefer-dist \
    \
    && echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini \
    \
    && ln -sfv /phpinsights/bin/phpinsights /usr/bin/phpinsights

WORKDIR /app

ENTRYPOINT ["phpinsights"]
