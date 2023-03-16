#!/usr/bin/with-contenv /bin/bash

set -e

composer=${COMPOSER_BIN:-/usr/local/sbin/composer}
export COMPOSER_HOME=/var/local/composer

cd /var/www

if [ ! -d "vendor" ];then
    if [[ "$DEBUG" != "" ]];then
        "${composer}" install --prefer-dist --no-interaction
    else
        "${composer}" install --no-scripts --no-dev -o --prefer-dist --no-interaction
    fi
fi

# Clear the response cache
./vendor/bin/laminas cache:clear-response

# Fix permissions for files that will be touched by the web user (data directory).
# The directory needs to be owned by the web user, and allow r/w privileges to
# allow writing (particularly for SQLite databases).
chown -R zendphp:zendphp data/cache
chmod 0775 data/cache
chmod -R u+rw data/cache
