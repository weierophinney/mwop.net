#!/bin/bash

set -e

composer=${COMPOSER:-/usr/local/sbin/composer}
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
"${composer}" clean:blog-cache # really only necessary when running locally

# Build homepage assets
if [ ! -f "data/homepage.posts.php" ] || [ ! -f "data/github-feed.json" ];then
    "${composer}" build:homepage
fi

# Fetch photo database
if [ ! -f "data/photos.db" ];then
    ./vendor/bin/laminas photo:fetch-db
fi

# Prepare initial comics
if [ ! -f "data/comics.phtml" ];then
    "${composer}" build:comics
fi

# Fix permissions for files that will be touched by the web user
chgrp zendphp data/github-feed.json data/homepage.posts.json data/comics.phtml data/photos.db
chmod g+rw data/github-feed.json data/homepage.posts.json data/comics.phtml data/photos.db
