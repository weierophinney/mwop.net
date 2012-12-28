#!/bin/bash
if [ `date +%H` == "05" ]
then
    (cd $OPENSHIFT_REPO_DIR ; /usr/local/zend/bin/php public/index.php phlycomic fetch all ; /usr/local/zend/bin/php public/index.php phlysimplepage cache clear --page=pages/comics )
fi
