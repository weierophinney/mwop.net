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
    if ! $(COMPOSER_HOME=/var/cache/composer && composer install --quiet --no-ansi --no-interaction --no-progress --no-scripts --no-plugins); then
        echo "[FAILED] Failed performing composer dev install" ;
        exit 1 ;
    fi

    # Setting mwop.net.php permissions
    echo "Setting mwop.net.php permissions" ;
    chmod 750 bin/mwop.net.php ;

    # Running build process
    echo "Running build process" ;
    if ! $(composer build); then
        echo "[FAILED] Failed building application" ;
        exit 1 ;
    fi

    # After the build, we optimize the installation
    echo "Executing composer (prod)" ;
    if ! $(COMPOSER_HOME=/var/cache/composer && composer install --quiet --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-plugins --optimize-autoloader); then
        echo "[FAILED] Failed performing composer production install" ;
        exit 1 ;
    fi
)

if [ $? -ne 0 ] ; then
    echo "[FAILED] One or more build tasks failed" ;
    exit 1 ;
fi

echo "[DONE] after-install-www-data.sh"
exit 0
