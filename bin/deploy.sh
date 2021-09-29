#!/bin/bash

set -e

if [ "${GITHUB_TOKEN}" == "" ] || [ "${GITHUB_USERNAME}" == "" ];then
    echo "Missing github login information"
    exit 1;
fi

if [ $# -lt 2 ];then
    echo "Missing required arguments"
    echo ""
    echo "Usage:"
    echo "  ${0} <repo> <sha>"
    echo ""
    echo "where <repo> is a repository under github.com/${GITHUB_USERNAME}, and"
    echo "<sha> is the commit to deploy."
    exit 1;
fi

# Get just the repo name
REPO=${1#*/}
SHA=$2
BASEDIR="/var/web/${REPO}"
# This directory must exist; usually it will be a mount to S3-compatible
# object storage
SITE_CONFIG_DIR="/mnt/site-config"
PREVIOUS=

# Prepare deployment directory, if it does not exist
if [ ! -d "${BASEDIR}" ];then
    echo "Creating deployment directory ${BASEDIR}"
    mkdir -p "${BASEDIR}"
fi

# Memoize "current" directory as PREVIOUS, if it exists
if [ -d "${BASEDIR}/current" ];then
    PREVIOUS=$(realpath "${BASEDIR}/current")
fi

# Prepare new release
echo "Preparing release directory based on commit ${SHA}"
DEPLOY_DIR="${BASEDIR}/${SHA}"
git clone --depth=1 --recurse-submodules "https://${GITHUB_USERNAME}:${GITHUB_TOKEN}@github.com/${GITHUB_USERNAME}/${REPO}.git" "${DEPLOY_DIR}"
cd "${DEPLOY_DIR}"
git checkout "${SHA}"

# Get env file
if [ -f "${DEPLOY_DIR}/env-version" ];then
    echo "Found env-version file; fetching production env"
    ENV_FILE="${SITE_CONFIG_DIR}/$(cat "${DEPLOY_DIR}/env-version")"

    if [ ! -f "${ENV_FILE}" ];then
        echo "FAILED - site config file specified in env-version not found"
        exit 1;
    fi

    cp "${ENV_FILE}" "${DEPLOY_DIR}/.env"
fi

# Build
echo "Building containers"
cd "${DEPLOY_DIR}"
if [ -f "${DEPLOY_DIR}/.deploy/pre-build.sh" ];then
    # This can be used to do things like create volumes
    echo "- Executing pre-build step"
    /bin/bash .deploy/pre-build.sh
fi
docker-compose build

# Deploy
# Stop previous
if [ "${PREVIOUS}" != "" ];then
    echo "Stopping previous deployment"
    cd "${PREVIOUS}"
    docker-compose down
fi

# Start new
echo "Starting deployment"
cd "${DEPLOY_DIR}"

set +e
if ! docker-compose up -d;then
    # FAILURE
    echo "FAILED deploying ${SHA}; rolling back"
    docker-compose down
    if [ "${PREVIOUS}" != "" ];then
        cd "${PREVIOUS}"
        docker-compose up -d
    fi
    exit 1
fi

# SUCCESS
cd "${BASEDIR}"
if [ "${PREVIOUS}" != "" ];then
    ln -fsn "$(basename "${PREVIOUS}")" previous
fi
ln -fsn "${SHA}" current
echo "SUCCESS deploying ${SHA}"
