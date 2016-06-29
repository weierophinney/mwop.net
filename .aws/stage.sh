#!/bin/bash
(
    cd /var/www/mwop.net ;
    cp /var/www/config/php/*.* config/autoload/ ;
    COMPOSER_HOME=/var/cache/composer composer install --no-ansi --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader ;
    php bin/mwop.net.php seed-blog-db ;
    php bin/mwop.net.php tag-cloud ;
    php bin/mwop.net.php feed-generator ;
    php bin/mwop.net.php cache-posts ;
    php bin/mwop.net.php github-links ;
    php vendor/bin/phly-comic.php fetch-all --output=data/comics.mustache ;
    npm install ;
    grunt ;
    rm -Rf node_modules ;
)
