# DOCKER-VERSION        1.3

# Build Swoole
FROM cr.zend.com/zendphp/8.0:ubuntu-20.04-cli as swoole

## Prepare image
ARG SWOOLE_VERSION=4.7.2
ARG TIMEZONE=UTC
ENV TZ=$TIMEZONE
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN set -e; \
    apt-get update; \
    apt-get install -y php8.0-zend-dev libcurl4-openssl-dev; \
    mkdir /workdir; \
    cd /workdir; \
    curl -L -o swoole-src-${SWOOLE_VERSION}.tgz https://github.com/openswoole/swoole-src/archive/refs/tags/v${SWOOLE_VERSION}.tar.gz; \
    tar xzf swoole-src-${SWOOLE_VERSION}.tgz; \
    cd swoole-src-${SWOOLE_VERSION}; \
    phpize; \
    ./configure \
        --enable-swoole \
        --enable-sockets \
        --enable-http2 \
        --enable-swoole-json \
        --enable-swoole-curl; \
    make; \
    make install

# Build assets
FROM node:16.13 as assets
RUN set -e; \
    echo "Installing agentkeepalive NPM module (required for npm upgrade)"; \
    npm install -g agentkeepalive --save; \
    echo "Upgrading npm to latest version"; \
    npm install -g npm@latest; \
    echo "Installing Grunt"; \
    npm install -g grunt-cli

COPY assets /assets
WORKDIR assets
RUN set -e; \
    if [ -d "node_modules" ];then \
        echo "Removing existing installed node modules"; \
        rm -rf node_modules; \
    fi; \
    echo "Installing asset dependencies"; \
    npm install; \
    echo "Building assets"; \
    grunt

# Build the PHP container
FROM cr.zend.com/zendphp/8.0:ubuntu-20.04-cli

## Install Swoole
COPY --from=swoole /usr/lib/php/8.0-zend/openswoole.so /usr/lib/php/8.0-zend/openswoole.so
COPY --from=swoole /usr/include/php/8.0-zend/ext/openswoole /usr/include/php/8.0-zend/ext/openswoole
RUN set -e; \
    echo "extension=openswoole.so" > /etc/zendphp/cli/conf.d/60-swoole.ini

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
COPY --from=assets /assets/build/js /var/www/public/js
COPY --from=assets /assets/build/css /var/www/public/css

## Customize PHP runtime according
## to the given building arguments
RUN ZendPHPCustomizeWithBuildArgs.sh

## Expose 9001
EXPOSE 9001

## Set working directory
WORKDIR /var/www

## Override entrypoint to use s6
ENV COMPOSER=/usr/local/sbin/composer
ENV COMPOSER_HOME=/var/local/composer
ENTRYPOINT ["/init"]
