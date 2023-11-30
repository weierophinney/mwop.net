#!/bin/bash

set -e

if ! docker volume ls | grep -q "mwop_net_redis";then
    docker volume create mwop_net_redis
fi

if ! docker volume ls | grep -q "mwop_net_shared_data";then
    docker volume create mwop_net_shared_data
fi

if ! docker volume ls | grep -q "mwop_net_zendhq_db";then
    docker volume create mwop_net_zendhq_db
fi

for dockerfile in nginx php; do
    awk -v "template=$(cat "./.docker/${dockerfile}.prod-template.Dockerfile")" "{sub(/## TEMPLATED ##/,template)}1" "./.docker/${dockerfile}.Dockerfile" > "./.docker/${dockerfile}.prod.Dockerfile"
done
