#!/bin/bash

set -e

if [ $# -eq 0 ];then
    echo "Please specify a file containing the new production ENV to upload"
    exit 1
fi

ENV_FILE=$1

if [ ! -f "${ENV_FILE}" ];then
    echo "Cannot find ${ENV_FILE} in working directory"
    exit 1
fi

UPLOAD_NAME="mwop_net-$(date +%Y-%m-%d)"

if [ $# -ge 2 ];then
    UPLOAD_NAME="${2}"
fi

command -v s3cmd
if [ $? > 0 ];then
    s3cmd put "${ENV_FILE}" "s3://cloud-mwop-net/site-config/${UPLOAD_NAME}"
else
    awsdo s3 cp "${ENV_FILE}" "s3://cloud-mwop-net/site-config/${UPLOAD_NAME}"
fi

echo "${UPLOAD_NAME}" > env-version

echo "Uploaded ${ENV_FILE} to production config as ${UPLOAD_NAME} and updated env-version"
