# DOCKER-VERSION        1.3

# Build the PHP container
FROM cr.zend.com/zendphp/8.1:alpine-3.18-fpm

## Customizations
ARG TIMEZONE=UTC
ARG INSTALL_COMPOSER=true
ARG SYSTEM_PACKAGES
ARG ZEND_EXTENSIONS_LIST="bz2 curl dom imagick intl mbstring opcache pcntl pdo_sqlite simplexml sqlite3 tidy xml xmlwriter xsl zendhq zip"
ARG PECL_EXTENSIONS_LIST
ARG POST_BUILD_BASH

## Prepare tzdata
ENV TZ=$TIMEZONE
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

## Create working directory and composer home
RUN set -e; \
    mkdir -p /var/local/composer
ENV COMPOSER_BIN=/usr/local/sbin/composer
ENV COMPOSER_HOME=/var/local/composer

## Customize PHP runtime according
## to the given building arguments
## Also, install shared libraries required by OpenSwoole
RUN set -e; \
    ZendPHPCustomizeWithBuildArgs.sh

## TEMPLATED ##

## Set working directory
WORKDIR /var/www

## Override entrypoint to use s6
ENTRYPOINT ["/init"]
CMD ["php-fpm"]
