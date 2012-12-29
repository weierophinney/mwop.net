#!/bin/bash
MINUTES=`date +%M`
INTERVALS=("00" "15" "30" "45")

for i in "00" "15" "30" "45";do
    if [ "$MINUTES" == "$i" ];then
        (cd $OPENSHIFT_REPO_DIR ; /usr/local/zend/bin/php public/index.php githubfeed fetch )
    fi
done
