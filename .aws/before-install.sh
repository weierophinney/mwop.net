#!/bin/bash
#######################################################################
# System dependencies
#######################################################################

# Install needed dependencies
echo "Updating system dependencies"
apt-get update
apt-get install -y nginx php7.0 php7.0-bcmath php7.0-bz2 php7.0-cli php7.0-ctype php7.0-curl php7.0-dom php7.0-fileinfo php7.0-fpm php7.0-gd php7.0-iconv php7.0-intl php7.0-json php7.0-mbstring php7.0-pdo php7.0-pdo-sqlite php7.0-phar php7.0-readline php7.0-simplexml php7.0-sockets php7.0-sqlite3 php7.0-tidy php7.0-tokenizer php7.0-xml php7.0-xsl php7.0-xmlreader php7.0-xmlwriter php7.0-zip npm python3-pip

# aws cli
echo "Installing awscli"
pip3 -q install awscli

# Get Composer, and install to /usr/local/bin
if [ ! -f "/usr/local/bin/composer" ];then
    echo "Installing composer"
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e115a8dc7871f15d853148a7fbac7da27d6c0030b848d9b3dc09e2a0388afed865e6a3d6b3c0fad45c48e2b5fc1196ae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
else
    echo "Updating composer"
    /usr/local/bin/composer self-update -q --stable --no-ansi --no-interaction
fi

# Create a COMPOSER_HOME directory for the application
if [ ! -d "/var/cache/composer" ];then
    echo "Creating composer cache directory"
    mkdir -p /var/cache/composer
    chown www-data.www-data /var/cache/composer
fi

# Get private configuration
if [ ! -d "/var/www/config" ];then
    echo "Creating production configuration cache"
    mkdir -p /var/www/config
fi
echo "Syncing production configuration settings"
(cd /var/www/config && aws s3 sync s3://config.mwop.net .)

# Make a log directory for php-fpm
if [ ! -d "/var/log/php" ];then
    echo "Creating PHP log directory"
    mkdir -p /var/log/php
fi
echo "Ensuring PHP log directory is writable by www-data user"
chown -R www-data.www-data /var/log/php
chmod -R ug+rwX /var/log/php

# Ensure node is symlinked
if [ ! -L /usr/local/bin/node ];then
    ln -s /usr/bin/nodejs /usr/local/bin/node
fi

# Install grunt globally
if [ ! -f "/usr/local/bin/grunt" ];then
    echo "Installing grunt"
    npm install -g grunt-cli
else
    echo "Updating grunt"
    npm update -g grunt-cli
fi

# Ensure we can run npm as www-data
if [ ! -d "/var/www/.npm" ];then
    echo "Ensuring www-data may run npm"
    mkdir -p /var/www/.npm
    chown www-data.www-data /var/www/.npm
    chmod o-X /var/www/.npm
    chmod ug+rwX /var/www/.npm
fi

echo "[DONE] before-install.sh"
exit 0
