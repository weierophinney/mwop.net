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
    COMPOSER_HOME=/var/cache/composer && composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader ;

    # Setting mwop.net.php permissions
    echo "Setting mwop.net.php permissions" ;
    chmod 750 bin/mwop.net.php ;

    # Running build process
    echo "Running build process" ;
    composer build ;
)

echo "[DONE] after-install-www-data.sh"
exit 0
