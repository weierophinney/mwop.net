# DOCKER-VERSION        1.3.2

FROM php:7.2-fpm

# System dependencies
RUN apt-get update && \
  apt-get install -y cron git libbz2-dev libicu-dev libtidy-dev libxslt1-dev zlib1g-dev && \
  rm -rf /var/lib/apt/lists/*

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) bcmath bz2 intl opcache pcntl sockets tidy xsl zip

# Install composer
COPY etc/bin/getcomposer.sh /usr/local/bin/
RUN /usr/local/bin/getcomposer.sh

# Crontab
COPY etc/cron.d/www-data /etc/cron.d/

# Project files
COPY etc/bin/php-fpm-entrypoint /usr/local/bin/
RUN mkdir /var/www/mwop.net
ADD bin /var/www/mwop.net/bin
ADD config /var/www/mwop.net/config
ADD data /var/www/mwop.net/data
ADD public /var/www/mwop.net/public
ADD src /var/www/mwop.net/src
ADD templates /var/www/mwop.net/templates
COPY composer.json /var/www/mwop.net/
COPY composer.lock /var/www/mwop.net/

# Reset "local"/development config files
RUN rm -f /var/www/mwop.net/config/development.config.php && \
  rm /var/www/mwop.net/config/autoload/*.local.php && \
  mv /var/www/mwop.net/config/autoload/local.php.dist /var/www/mwop.net/config/autoload/local.php

# Build project
WORKDIR /var/www/mwop.net
RUN composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader && \
  composer build-php-fpm && \
  chown -R www-data.www-data /var/www/mwop.net/data

# PHP config (performed late so as not to affect earlier layers)
COPY etc/php/mwop.ini /usr/local/etc/php/conf.d/
COPY etc/php/www.conf /usr/local/etc/php-fpm.d/

# Entry point script (does not change often)
COPY etc/bin/php-fpm-entrypoint /usr/local/bin/

EXPOSE 9000
CMD ["php-fpm-entrypoint"]
