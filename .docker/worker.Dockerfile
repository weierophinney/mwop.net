# DOCKER-VERSION        1.3

# Build the PHP container
FROM cr.zend.com/zendphp/8.1:alpine-3.16-cli

## Customizations
ARG TIMEZONE=UTC
ARG INSTALL_COMPOSER=true
ARG SYSTEM_PACKAGES="supervisor"
ARG ZEND_EXTENSIONS_LIST="bz2 curl imagick intl mbstring opcache pdo_sqlite simplexml sqlite3 tidy xsl zip"
ARG PECL_EXTENSIONS_LIST
ARG POST_BUILD_BASH

## Create working directory and composer home
RUN set -e; \
    mkdir -p /var/www /var/local/composer
ENV COMPOSER_BIN=/usr/local/sbin/composer
ENV COMPOSER_HOME=/var/local/composer

## Customize PHP runtime according
## to the given building arguments
RUN set -e; \
    ZendPHPCustomizeWithBuildArgs.sh

## Set working directory
WORKDIR /var/www

## Override entrypoint to use s6
ENTRYPOINT ["/init"]
