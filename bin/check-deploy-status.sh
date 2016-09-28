#!/usr/bin/zsh

DEPLOY_ID=$1

if ! [[ "${DEPLOY_ID}" =~ '^d-[A-Z0-9]{8}$' ]];then
    SCRIPT_PATH=$(readlink -f $(dirname $0))
    DEPLOY_ID=$(${SCRIPT_PATH}/get-deploy-id.sh)
    if [[ "${DEPLOY_ID}" =~ "No current deployments in progress" ]];then
        echo ${DEPLOY_ID}
        exit 0
    fi
fi

aws deploy get-deployment --deployment-id ${DEPLOY_ID} --query "deploymentInfo.status"
