# DOCKER-VERSION        1.3

# Build Swoole
FROM cr.zend.com/zendphp/8.1:ubuntu-20.04-cli as swoole

## Prepare image
ARG SWOOLE_VERSION=4.10.0
ARG TIMEZONE=UTC
ENV TZ=$TIMEZONE
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN set -e; \
    apt-get update; \
    apt-get install -y php8.1-zend-dev libcurl4-openssl-dev; \
    mkdir /workdir; \
    cd /workdir; \
    curl -L -o openswoole-${SWOOLE_VERSION}.tgz https://pecl.php.net/get/openswoole-${SWOOLE_VERSION}.tgz; \
    tar xzf openswoole-${SWOOLE_VERSION}.tgz; \
    cd openswoole-${SWOOLE_VERSION}; \
    phpize8.1-zend; \
    ./configure \
        --with-php-config=/usr/bin/php-config8.1-zend \
        --enable-http2 \
        --enable-openssl \
        --enable-sockets \
        --enable-swoole \
        --enable-swoole-curl \
        --enable-swoole-json; \
    make; \
    make install

# Build assets
FROM node:16.13 as assets
RUN set -e; \
    echo "Installing make (required for building assets)"; \
    apt-get update; \
    apt-get install -y make; \
    echo "Installing agentkeepalive NPM module (required for npm upgrade)"; \
    npm install -g agentkeepalive --save; \
    echo "Upgrading npm to latest version"; \
    npm install -g npm@latest; \
    echo "Installing PostCSS"; \
    npm install -g postcss-cli; \
    echo "Creating build directory"; \
    mkdir /build

COPY assets /build/assets
COPY src /build/src
COPY templates /build/templates

WORKDIR /build/assets
RUN set -e; \
    if [ -d "node_modules" ];then \
        echo "Removing existing installed node modules"; \
        rm -rf node_modules; \
    fi; \
    echo "Installing asset dependencies"; \
    npm install; \
    echo "Building assets"; \
    make assets

# Build the PHP container
FROM cr.zend.com/zendphp/8.1:ubuntu-20.04-cli

## Customizations
ARG TIMEZONE=UTC
ARG INSTALL_COMPOSER=false
ARG SYSTEM_PACKAGES
ARG ZEND_EXTENSIONS_LIST
ARG PECL_EXTENSIONS_LIST
ARG POST_BUILD_BASH

## Prepare tzdata
ENV TZ=$TIMEZONE
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

## Create working directory and composer home
RUN set -e; \
    mkdir -p /var/www/public/js /var/www/public/css /var/local/composer

## Install assets
COPY --from=assets /build/assets/dist /var/www/public/assets

## Customize PHP runtime according
## to the given building arguments
RUN ZendPHPCustomizeWithBuildArgs.sh

## Install Swoole
COPY --from=swoole /usr/lib/php/8.1-zend/openswoole.so /usr/lib/php/8.1-zend/openswoole.so
COPY --from=swoole /usr/include/php/8.1-zend/ext/openswoole /usr/include/php/8.1-zend/ext/openswoole
RUN set -e; \
    echo "extension=openswoole.so" > /etc/zendphp/cli/conf.d/60-swoole.ini

## Expose 9001
EXPOSE 9001

## Set working directory
WORKDIR /var/www

## Override entrypoint to use s6
ENV COMPOSER_BIN=/usr/local/sbin/composer
ENV COMPOSER_HOME=/var/local/composer
ENTRYPOINT ["/init"]
