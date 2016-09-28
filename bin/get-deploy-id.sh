#!/usr/bin/zsh
DEPLOY_ID=$(aws deploy list-deployments --application-name mwop.net --deployment-group-name mwop.net --include-only-statuses Queued InProgress --query "deployments[0]")

if [[ 'null' == "$DEPLOY_ID" ]]; then
    echo "No current deployments in progress"
    exit 0;
fi

DEPLOY_ID="${DEPLOY_ID%\"}"
DEPLOY_ID="${DEPLOY_ID#\"}"

echo ${DEPLOY_ID}
