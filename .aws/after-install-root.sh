#!/bin/bash
# Actions to execute as root after install.

SCRIPT_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
CRONTAB_PATH=/var/spool/cron/crontabs/www-data

# Install crontab for www-data
chmod -R o-rwX /var/www/mwop.net
cp ${SCRIPT_PATH}/crontab ${CRONTAB_PATH} && chown www-data.crontab ${CRONTAB_PATH} && chmod 600 ${CRONTAB_PATH}
