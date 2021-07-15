FROM php:7.4-fpm

#ARG UID=1000
#ARG GID=1000
#
#RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data && chown -R www-data: /var/www

ENV TZ=Europe/Moscow

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
    && apt-get update && apt-get install -y \
            libfreetype6-dev \
            libjpeg62-turbo-dev \
            libpng-dev \
            libcurl4-gnutls-dev \
            libpq-dev \
            libonig-dev \
            libxml2-dev \
            curl \
            git \
            libzip-dev \
    && docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-configure curl \
    && docker-php-ext-install -j$(nproc) curl \
    && docker-php-ext-install -j$(nproc) zip \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j$(nproc) pgsql \
    && docker-php-ext-install -j$(nproc) pdo_pgsql \
    && docker-php-ext-install -j$(nproc) mbstring \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install -j$(nproc) json \
    && docker-php-ext-configure xml \
    && docker-php-ext-install -j$(nproc) xml \
    && docker-php-ext-configure gd  \
    && docker-php-ext-install -j$(nproc) mysqli \
    && docker-php-ext-install -j$(nproc) pdo_mysql \
    && docker-php-ext-install -j$(nproc) gd

RUN apt-get update && apt-get install -y \
        libmagickwand-dev \
        && pecl install xdebug \
        && docker-php-ext-enable xdebug \
        && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.client_host = host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.idekey = IDEKEY" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && rm -rf /var/lib/apt/lists/*
#    && pecl install imagick \
#    && docker-php-ext-enable imagick

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer --version=1.10.20



#USER www-data

RUN composer global require "fxp/composer-asset-plugin:^1.3.1"

EXPOSE 80

ENTRYPOINT /app/spiral serve  -d


