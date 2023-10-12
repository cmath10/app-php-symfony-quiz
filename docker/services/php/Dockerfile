FROM php:8.2.11-fpm-alpine3.18

ENV LANG "C.UTF-8"
ENV LANGUAGE "C.UTF-8"
ENV LC_ALL "C.UTF-8"

# Install dependencies
RUN set -ex \
    && apk update \
    && apk add --no-cache \
        bash \
        freetype \
        git \
        icu-libs \
        imap-dev \
        libbz2 \
        libjpeg-turbo-dev \
        libmcrypt-dev \
        libpng \
        libpq \
        libxml2 \
        libxpm-dev \
        libxslt-dev \
        libzip-dev \
        linux-headers \
        lz4-dev \
        nano \
        patch \
        unzip \
        wget \
        zlib \
        zstd-dev

# User utils
RUN apk --no-cache add shadow

RUN echo "apc.enable_cli=1" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini

# Install PHP extensions
RUN set -ex \
    && apk add --no-cache --virtual .build-deps \
        autoconf \
        bzip2-dev \
        file \
        freetype-dev \
        gcc \
        krb5-dev \
        krb5-conf \
        libc-dev \
        libpng-dev \
        make \
        postgresql-dev \
        zlib-dev \
    && pecl install apcu igbinary msgpack xdebug \
    && docker-php-ext-enable apcu igbinary xdebug \
    && yes | pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install \
        bcmath \
        bz2 \
        calendar \
        intl \
        imap \
        gd \
        opcache \
        pgsql \
        pdo_pgsql \
        soap \
        xsl \
        zip \
    && apk del --no-network .build-deps

RUN echo "extension=msgpack.so" >> /usr/local/etc/php/conf.d/docker-php-ext-msgpack.ini

# Composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --filename=composer  --version=2.6.5 --install-dir=/usr/local/bin && \
    php /usr/local/bin/composer clear-cache

RUN apk add --no-cache \
    openssh-client \
    su-exec

# Fix files permissions
RUN chown www-data:www-data /var/www
RUN groupmod -o -g 1000 www-data && usermod -o -u 1000 www-data
RUN groupadd php_conf
RUN usermod -a -G php_conf www-data
RUN chgrp -R php_conf "$PHP_INI_DIR/conf.d"
RUN chmod g+w -R "$PHP_INI_DIR/conf.d"

USER www-data

WORKDIR /var/www/app
