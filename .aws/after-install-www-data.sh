#!/bin/bash
#######################################################################
# Application preparation
#######################################################################

(
    cd /var/www/mwop.net ;

    # Copy in the production local configuration
    cp /var/www/config/php/*.* config/autoload/ ;

    # Execute a composer installation
    COMPOSER_HOME=/var/cache/composer composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader ;

    # Seed the blog posts database
    php bin/mwop.net.php seed-blog-db ;

    # Create the tag cloud
    php bin/mwop.net.php tag-cloud ;

    # Create the feeds
    php bin/mwop.net.php feed-generator ;

    # Cache the blog posts
    php bin/mwop.net.php cache-posts ;

    # Create the initial set of github links for the front page
    php bin/mwop.net.php github-links ;

    # Create the initial set of comics
    php vendor/bin/phly-comic.php fetch-all --output=data/comics.mustache ;

    # Compile CSS and JS
    npm install ;
    grunt ;
    rm -Rf node_modules ;
)
