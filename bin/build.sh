#!/bin/bash

set -e

if [ ! -d "vendor" ];then
    if [[ "$DEBUG" != "" ]];then
        composer install --prefer-dist --no-interaction --ignore-platform-req=php
    else
        composer install --no-scripts --no-dev -o --prefer-dist --no-interaction --ignore-platform-req=php
    fi
fi

# Build the blog
if [ ! -f "data/posts.db" ] || [ ! -f "data/tag-cloud.phtml" ] || [ ! -f "public/search_terms.json" ];then
    composer build:blog
fi
composer clean:blog-cache # really only necessary when running locally

# Build homepage assets
if [ ! -f "config/autoload/homepage.local.php" ] || [ ! -f "data/github-links.phtml" ];then
    composer build:homepage
fi

# Prepare initial comics
if [ ! -f "data/comics.phtml" ];then
    composer build:comics
fi
