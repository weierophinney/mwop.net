#!/bin/bash
#######################################################################
# Application preparation
#######################################################################

(
    cd /var/www/mwop.net ;

    # Copy in the production local configuration
    echo "Syncing production configuration" ;
    cp /var/www/config/php/*.* config/autoload/ ;

    # Execute a composer installation
    echo "Executing composer" ;
    COMPOSER_HOME=/var/cache/composer composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader ;

    # Clear existing cache files
    echo "Clearing cache files" ;
    php bin/mwop.net.php clear-cache ;

    # Seed the blog posts database
    echo "Seeding the blog database" ;
    php bin/mwop.net.php seed-blog-db ;

    # Create the tag cloud
    echo "Creating the tag cloud" ;
    php bin/mwop.net.php tag-cloud ;

    # Create the feeds
    echo "Creating feeds" ;
    php bin/mwop.net.php feed-generator ;

    # Cache the blog posts
    echo "Caching posts" ;
    php bin/mwop.net.php cache-posts ;

    # Create the initial set of github links for the front page
    echo "Fetching GitHub activity" ;
    php bin/mwop.net.php github-links ;

    # Create the initial set of recent blog posts for the front page
    echo "Fetching feeds for the homepage" ;
    php bin/mwop.net.php homepage-feeds ;

    # Create the initial set of comics
    echo "Fetching comics" ;
    php vendor/bin/phly-comic.php fetch-all --output=data/comics.mustache ;

    # Compile CSS and JS
    echo "Compiling assets" ;
    npm install ;
    grunt ;
    rm -Rf node_modules ;
)
