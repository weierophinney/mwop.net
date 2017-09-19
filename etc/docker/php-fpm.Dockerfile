# DOCKER-VERSION        1.3.2

FROM php:7.1-fpm

# System dependencies
# RUN apt-get -y install apt-utils apt-transport-https build-essential
RUN curl -sL https://deb.nodesource.com/setup_7.x | bash -
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get install -y cron nodejs git yarn libbz2-dev libicu-dev libtidy-dev libxslt1-dev zlib1g-dev

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) bcmath bz2 intl opcache pcntl sockets tidy xsl zip

# PHP config
COPY etc/php/mwop.ini /usr/local/etc/php/conf.d/
COPY etc/php/www.conf /usr/local/etc/php-fpm.d/
COPY etc/bin/php-fpm-entrypoint /usr/local/bin/

# Install composer
COPY etc/bin/getcomposer.sh /usr/local/bin/
RUN /usr/local/bin/getcomposer.sh

# Install Grunt
RUN yarn global add grunt

# Crontab
COPY etc/cron.d/www-data /var/spool/cron/crontabs/
RUN chown www-data.crontab /var/spool/cron/crontabs/www-data && chmod 600 /var/spool/cron/crontabs/www-data

# Project files
RUN mkdir /var/www/mwop.net
ADD bin /var/www/mwop.net/bin
ADD config /var/www/mwop.net/config
ADD data /var/www/mwop.net/data
ADD public /var/www/mwop.net/public
ADD src /var/www/mwop.net/src
ADD templates /var/www/mwop.net/templates
COPY Gruntfile.js /var/www/mwop.net/
COPY composer.json /var/www/mwop.net/
COPY composer.lock /var/www/mwop.net/
COPY package.json /var/www/mwop.net/
COPY yarn.lock /var/www/mwop.net/

# Reset "local"/development config files
RUN rm -f /var/www/mwop.net/config/development.config.php
RUN rm /var/www/mwop.net/config/autoload/*.local.php
RUN mv /var/www/mwop.net/config/autoload/local.php.dist /var/www/mwop.net/config/autoload/local.php

# Build project
WORKDIR /var/www/mwop.net
RUN composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader
RUN composer build

EXPOSE 9000
CMD ["php-fpm-entrypoint"]
