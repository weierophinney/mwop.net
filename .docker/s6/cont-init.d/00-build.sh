#!/usr/bin/with-contenv /bin/bash

set -e

composer=${COMPOSER_BIN:-/usr/local/sbin/composer}
export COMPOSER_HOME=/var/local/composer

cd /var/www

deploy_env="${DEPLOY_ENV:-prod}"

if [ ! -d "vendor" ];then
    if [[ "$DEBUG" != "" ]];then
        "${composer}" install --prefer-dist --no-interaction
    else
        "${composer}" install --no-scripts --no-dev -o --prefer-dist --no-interaction
    fi
fi

mkdir -p data/shared/feeds

if [[ "${deploy_env}" == "prod" ]]; then
    # Always rebuild blog, homepage, and comics on production deployment
    "${composer}" build:blog
    "${composer}" build:homepage
    "${composer}" build:comics
else
    # In other environments, only build when missing

    # Build the blog
    if [[ ! -f "data/shared/posts.db" || ! -f "data/shared/tag-cloud.phtml" ]];then
        "${composer}" build:blog
    fi

    # Build homepage assets
    if [[ ! -f "data/shared/homepage.posts.php" || ! -f "data/shared/github-feed.json" ]];then
        "${composer}" build:homepage
    fi

    # Prepare initial comics
    if [[ ! -f "data/shared/comics.phtml" ]];then
        "${composer}" build:comics
    fi
fi

# Copy photo database
if [[ ! -f "data/shared/photodb/photos.db" ]];then
    ./vendor/bin/laminas photo:fetch-db
fi

# Clear the response cache
./vendor/bin/laminas cache:clear-response

# Fix permissions for files that will be touched by the web user (data directory).
# The directory needs to be owned by the web user, and allow r/w privileges to
# allow writing (particularly for SQLite databases).
chown -R zendphp:zendphp data/cache
chmod 0775 data/cache
chmod -R u+rw data/cache
