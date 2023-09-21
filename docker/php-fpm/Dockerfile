FROM php:7.4-fpm-alpine

RUN apk update && \
    apk upgrade && \
    apk add --no-cache \
            postgresql-dev \
            git \
            unzip \
            zlib-dev \
            libxml2-dev \
            libzip-dev \
            libxslt-dev \
            freetype-dev \
            libjpeg-turbo-dev \
            libpng-dev \
            musl-dev && \
    docker-php-ext-install pdo_pgsql

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN chmod +x /usr/local/bin/install-php-extensions && \
    sync && \
    install-php-extensions gd \
      yaml \
      imagick \
      zip \
      intl \
      mysqli \
      pdo_mysql \
      sysvsem \
      mongodb \
      redis \
      @composer-2 \
      memcached

RUN mkdir -p /var/log/php-app/wd.local && \
    chown -R www-data /var/log/php-app/ && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    echo "error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT & ~E_USER_DEPRECATED" >> "$PHP_INI_DIR/php.ini" && \
    echo "memory_limit = 256M" > /usr/local/etc/php/conf.d/memory_limit.ini

ENV XDEBUG_VERSION=2.9.2
RUN apk --no-cache add --virtual .build-deps \
        g++ \
        autoconf \
        make && \
    pecl install xdebug-${XDEBUG_VERSION} && \
    docker-php-ext-enable xdebug && \
    apk del .build-deps && \
    rm -r /tmp/pear/* && \
    echo -e "xdebug.remote_enable=1\n\
        xdebug.remote_autostart=1\n\
        xdebug.remote_connect_back=0\n\
        xdebug.remote_port=9001\n\
        xdebug.idekey=\"VSCODE\"\n\
        xdebug.remote_log=/var/www/html/xdebug.log\n\
        xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/bestRk
