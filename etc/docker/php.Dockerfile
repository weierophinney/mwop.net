# DOCKER-VERSION        1.3.2

FROM mwop/phly-docker-php-swoole:7.2-alpine

# System dependencies
RUN apk update && apk add --no-cache dcron bzip2-dev icu-dev tidyhtml-dev libxml2-dev libxslt-dev

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) bcmath bz2 dom intl opcache pcntl sockets tidy xsl zip

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
