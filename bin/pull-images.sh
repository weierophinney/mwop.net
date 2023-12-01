#!/bin/bash

set -e

script_path="$( cd -- "$(dirname "$0")" >/dev/null 2>&1 ; pwd -P )"
app_path=$(realpath "${script_path}/..")

grep -P "^\s+image: [a-z0-9/._:-]+$" < "${app_path}/docker-compose.yml" | sed -r "s/\s+image:\s+//" | while IFS= read -r image
do
    echo "Pulling ${image}..."
    docker pull "${image}"
done

for dockerfile in "${app_path}/.docker/nginx.Dockerfile" "${app_path}/.docker/php.Dockerfile"; do
    grep -P "^FROM (\S+)" < "${dockerfile}" | sed -r "s/^FROM (\S+).*$/\1/" | while IFS= read -r image
    do
        echo "Pulling ${image}..."
        docker pull "${image}"
    done
done
