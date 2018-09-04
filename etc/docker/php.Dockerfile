# DOCKER-VERSION        1.3.2

FROM mwop/phly-docker-php-swoole:201809041014

# System dependencies
RUN apt-get update && \
  apt-get install -y cron && \
  rm -rf /var/lib/apt/lists/*

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) bcmath bz2 intl opcache pcntl sockets tidy xsl zip

# PHP configuration
COPY etc/php/mwop.ini /usr/local/etc/php/conf.d/999-mwop.ini

# Overwrite entrypoint
COPY etc/bin/php-entrypoint /usr/local/bin/entrypoint

# Crontab
COPY etc/cron.d/mwopnet /etc/cron.d/

# Project files
COPY bin /var/www/bin
COPY data /var/www/data
COPY public /var/www/public
COPY templates /var/www/templates
COPY composer.json /var/www/
COPY composer.lock /var/www/
COPY config /var/www/config
COPY src /var/www/src

# Reset "local"/development config files
RUN rm -f /var/www/config/development.config.php && \
  rm /var/www/config/autoload/*.local.php && \
  mv /var/www/config/autoload/local.php.dist /var/www/config/autoload/local.php

# Build project
WORKDIR /var/www
RUN composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader && \
  composer docker:site
