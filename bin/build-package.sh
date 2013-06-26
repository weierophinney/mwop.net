#!/bin/bash
MYDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
GITARCHIVE=$MYDIR/git-archive-all.sh
APPDIR="$MYDIR/../"
SHA1=$(git rev-parse HEAD)
VERSION=${SHA1:0:7}

# Stage package files based on current HEAD
mkdir -p /tmp/mwop.net/package
$( cd $APPDIR && $GITARCHIVE --format tar --prefix data/ /tmp/mwop.net/mwop.net.tar )
$( cd /tmp/mwop.net/package && tar xf ../mwop.net.tar && rm ../mwop.net.tar )
if [ ! -e "/tmp/mwop.net/package/data/deployment.xml" ]; then
    echo "Failed to export repository"
    exit 1
fi

# Run composer
$( cd /tmp/mwop.net/package/data && php composer.phar install --no-dev )

# Get "local" config
$( cd /tmp/mwop.net && git clone -b mwop.net.config git@github.com:weierophinney/site-settings.git ) 
if [ ! -e "/tmp/mwop.net/site-settings/README.md" ]; then
    echo "Failed to export site settings"
    exit 1
fi
$( cd /tmp/mwop.net && mv site-settings/*.php package/data/config/autoload/ )

# Update deployment.xml
$( cd /tmp/mwop.net/package && mv data/deployment.xml . && sed --in-place -e s/{SHA1}/"$VERSION"/ deployment.xml && zip -rq ../mwop.net.$VERSION.zpk . )

# Copy deployment package locally and cleanup
mv /tmp/mwop.net/mwop.net.$VERSION.zpk $APPDIR
rm -Rf /tmp/mwop.net

echo "ZPK is at $APPDIR/mwop.net.$VERSION.zpk"
