# DOCKER-VERSION        1.3.2

FROM mwop/phly-docker-php-swoole:7.2-alpine

# System dependencies
RUN echo 'http://dl-cdn.alpinelinux.org/alpine/v3.6/community' >> /etc/apk/repositories
RUN apk update && \
  apk add --no-cache \
    php7-bcmath \
    php7-bz2 \
    php7-dom \
    php7-intl \
    php7-opcache \
    php7-pcntl \
    php7-sockets \
    php7-xsl \
    php7-zip \
    'tidyhtml-dev==5.2.0-r1'

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) tidy

# PHP configuration
COPY etc/php/mwop.ini /usr/local/etc/php/conf.d/999-mwop.ini

# Overwrite entrypoint
COPY etc/bin/php-entrypoint /usr/local/bin/entrypoint

# Crontab
COPY etc/cron.d/mwopnet /etc/cron.d/

# Project files
COPY bin /var/www/bin
COPY composer.json /var/www/
COPY composer.lock /var/www/
COPY templates /var/www/templates
COPY config /var/www/config
COPY src /var/www/src
COPY data /var/www/data
COPY public /var/www/public

# Reset "local"/development config files
RUN rm -f /var/www/config/development.config.php && \
  rm /var/www/config/autoload/*.local.php && \
  mv /var/www/config/autoload/local.php.dist /var/www/config/autoload/local.php

# Build project
WORKDIR /var/www
RUN composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader && \
  composer docker:site
