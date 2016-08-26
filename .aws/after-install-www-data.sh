#!/bin/bash
#######################################################################
# Application preparation
#######################################################################

(
    cd /var/www/mwop.net ;

    # Copy in the production local configuration
    echo "Syncing production configuration" ;
    cp /var/www/config/php/*.* config/autoload/ ;

    # Execute a composer installation ; do a full install at first
    echo "Executing composer (dev)" ;
    $(COMPOSER_HOME=/var/cache/composer && composer install --quiet --no-ansi --no-interaction --no-progress --no-scripts --no-plugins) ;

    # Setting mwop.net.php permissions
    echo "Setting mwop.net.php permissions" ;
    chmod 750 bin/mwop.net.php ;

    # Running build process
    echo "Running build process" ;
    composer build ;

    # After the build, we optimize the installation
    echo "Executing composer (prod)" ;
    $(COMPOSER_HOME=/var/cache/composer && composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader) ;
)

echo "[DONE] after-install-www-data.sh"
exit 0
