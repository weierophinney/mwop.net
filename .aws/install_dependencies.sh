#!/bin/bash

SCRIPT_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Install needed dependencies
apt-get install -y nginx php7.0 php7.0-bcmath php7.0-bz2 php7.0-cli php7.0-ctype php7.0-curl php7.0-dom php7.0-fileinfo php7.0-fpm php7.0-gd php7.0-iconv php7.0-intl php7.0-json php7.0-mbstring php7.0-pdo php7.0-pdo-sqlite php7.0-phar php7.0-readline php7.0-simplexml php7.0-sockets php7.0-sqlite3 php7.0-tidy php7.0-tokenizer php7.0-xml php7.0-xsl php7.0-xmlreader php7.0-xmlwriter php7.0-zip npm

# aws cli
pip3 install awscli

# Get Composer, and install to /usr/local/bin
if [ ! -f "/usr/local/bin/composer" ];then
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
else
    /usr/local/bin/composer self-update --stable --no-ansi --no-interaction
fi


# Get private configuration
if [ ! -d "/var/www/config" ];then
    mkdir -p /var/www/config
fi
(cd /var/www/config && aws s3 sync s3://config.mwop.net .)
mv /var/www/config/ssl/*.* /etc/ssl/
(cd /etc/ssl && cat mwop.net.crt mwop.net.ca-bundle > mwop.net.chained.crt)

# Copy nginx configuration
cp ${SCRIPT_PATH}/mwop.net.conf /etc/nginx/sites-available/
if [ ! -e "/etc/nginx/sites-enabled/mwop.net.conf" ];then
    (cd /etc/nginx/sites-enabled && ln -s ../sites-available/mwop.net.conf .)
fi

# Copy php configuration
cp ${SCRIPT_PATH}/php.ini /etc/php/7.0/fpm/conf.d mwop.ini
cp ${SCRIPT_PATH}/php-fpm.conf /etc/php/7.0/fpm/pool.d/www.conf

# Make a log directory for php-fpm
if [ ! -d "/var/log/php" ];then
    mkdir -p /var/log/php
fi
chown -R www-data.www-data /var/log/php
chmod -R ug+rwX /var/log/php

# Install grunt globally
npm install -g grunt-cli
