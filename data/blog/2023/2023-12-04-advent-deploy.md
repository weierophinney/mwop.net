---
id: 2023-12-04-advent-deploy
author: matthew
title: 'Advent 2023: A deploy script'
draft: false
public: true
created: '2023-12-04T18:00:00-06:00'
updated: '2023-12-04T18:00:00-06:00'
tags:
    - advent2023
    - bash
    - devops
    - deployment
---

For the fourth day of 2023 advent blogging, I'm sharing a tool I've used to simplify deployment.

<!--- EXTENDED -->

I've tried a lot of things for easing deployment over the years, ranging from Zend Server to AWS Code Deploy, to [Deployer](https://deployer.org), and a whole lot of scripts I've rolled out on my own over the years.

I'm finding that as time goes by, simpler is better.

To that end, I've been using the following script, `deploy.sh`, to deploy a number of websites the last couple years.

It has minimal requirements:

- It requires the `GITHUB_TOKEN` and `GITHUB_USERNAME` ENV variables; this is so it can pull repositories as needed.
  I set these on my server.
- It accepts two arguments:
  - The GitHub repository it will be deploying
  - A commit SHA from that repo to deploy

From there, it does the following:

- It checks out the given repo at the given commit within a deployment tree.
- If there is an `env-version` file, it pulls that from a local configuration store, and inserts it as `.env` in the checkout.
  I have a script that will push a named env file with a revision into object storage, which is then mapped into the server; it copies from there based on the `env-version`.
- If the checkout has a `.deploy/build.sh` script, it uses that to build the application; otherwise, it runs `docker compose build`.
- It identifies the running application, if any, from the `current/` symlink in the deployment directory, and stops it.
  If that version has a `.deploy/stop.sh` script, it will run that; otherwise it runs `docker compose down`.
- If the new release checkout has a `.deploy/deploy.sh` script, it runs that; otherwise, it runs `docker compose up -d`.
- It symlinks that previously running application directory to `previous/`.
- It symlinks the new release to `current/`.
- If there are more than 5 releases present, it removes the oldest ones.

This gives me the flexibility to write just about anything I need for a given application, while keeping a very generic process otherwise (using `docker compose`).

On the github side of things, I register secrets for the SSH host, user, and key, and have a github action that uses those to SSH to the machine and execute that script..

The full script:

```bash
#!/bin/bash

set -e

deploy() {
    local release_path="$1"

    cd "${release_path}"
    if [ -f "${release_path}/.deploy/deploy.sh" ]; then
        /bin/bash .deploy/deploy.sh
    else
        echo "Starting deployment in ${release_path}"
        docker compose up -d
    fi
}

stop() {
    local release_path="$1"

    cd "${release_path}"
    if [ -f "${release_path}/.deploy/stop.sh" ]; then
        /bin/bash .deploy/stop.sh
    else
        echo "Stopping deployment in ${release_path}"
        docker compose down
    fi
}

cleanup() {
    local base_dir="$1"
    local current_release="$2"
    local release
    local listing=()
    local releases=()

    cd "${base_dir}"

    # Get listing of directories, sorted chronologically, newest first
    mapfile -t listing < <(ls -t -d -- */)

    # Filter out symlinks for current and previous, and what was just released
    for release in "${listing[@]}"; do
        case "${release}" in
            current/|previous/|"${current_release}/")
                true
                ;;
            *)
                releases+=( "${release}" )
                ;;
        esac
    done

    # Keep only the most recent 5 releases
    if [[ ${#releases[@]} -gt 5 ]]; then
        echo "Removing old releases"
        for release in "${releases[@]:5}"; do
            echo "- Removing ${release}"
            rm -rf "${base_dir:?}/${release}"
        done
    fi
}

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
cd "${DEPLOY_DIR}"

if [ -f "${DEPLOY_DIR}/.deploy/build.sh" ]; then
    /bin/bash .deploy/build.sh
else
    if [ -f "${DEPLOY_DIR}/.deploy/pre-build.sh" ];then
        # This can be used to do things like create volumes
        echo "Building containers"
        echo "- Executing pre-build step"
        /bin/bash .deploy/pre-build.sh
    fi
    docker compose build
fi

# DEPLOY

# Stop previous
if [ "${PREVIOUS}" != "" ];then
    stop "${PREVIOUS}"
fi

# Start new
echo "Starting deployment"

set +e
if ! deploy "${DEPLOY_DIR}"; then
    echo "FAILED deploying ${SHA}; rolling back"
    echo "- Stopping deployment"
    stop "${DEPLOY_DIR}"

    if [ "${PREVIOUS}" != "" ];then
        echo "- Restarting previous deployment"
        deploy "${PREVIOUS}"
    fi
    exit 1
fi
set -e

# SUCCESS
cd "${BASEDIR}"
if [ "${PREVIOUS}" != "" ];then
    ln -fsn "$(basename "${PREVIOUS}")" previous
fi
ln -fsn "${SHA}" current
echo "SUCCESS deploying ${SHA}"

# CLEANUP
cleanup "${BASEDIR}" "${SHA}"
```
