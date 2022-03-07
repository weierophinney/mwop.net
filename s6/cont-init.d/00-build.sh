#!/usr/bin/with-contenv /bin/bash

set -e

composer=${COMPOSER_BIN:-/usr/local/sbin/composer}
export COMPOSER_HOME=/var/local/composer

if [ ! -d "vendor" ];then
    if [[ "$DEBUG" != "" ]];then
        "${composer}" install --prefer-dist --no-interaction
    else
        "${composer}" install --no-scripts --no-dev -o --prefer-dist --no-interaction
    fi
fi

# Build the blog
if [ ! -f "data/posts.db" ] || [ ! -f "data/tag-cloud.phtml" ] || [ ! -f "public/search_terms.json" ];then
    "${composer}" build:blog
fi

# Build homepage assets
if [ ! -f "data/homepage.posts.php" ] || [ ! -f "data/github-feed.json" ];then
    "${composer}" build:homepage
fi

# Prepare initial comics
if [ ! -f "data/comics.phtml" ];then
    "${composer}" build:comics
fi

# Copy photo database
if [ ! -f "data/photos.db" ];then
    ./vendor/bin/laminas photo:fetch-db
fi

# Clear the response cache
./vendor/bin/laminas cache:clear-response

# Fix permissions for files that will be touched by the web user (data directory).
# The directory needs to be owned by the web user, and allow r/w privileges to
# allow writing (particularly for SQLite databases).
chown -R zendphp.zendphp data
chmod 0775 data
chmod -R u+rw data
