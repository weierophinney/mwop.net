#!/bin/bash
#######################################################################
# Install crontab after successful installation
#######################################################################

SCRIPT_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
CRONTAB_PATH=/var/spool/cron/crontabs/www-data

cp ${SCRIPT_PATH}/crontab ${CRONTAB_PATH} && chown www-data.crontab ${CRONTAB_PATH} && chmod 600 ${CRONTAB_PATH}
