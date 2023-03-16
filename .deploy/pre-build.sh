#!/bin/bash

set -e

if ! docker volume ls | grep -q "mwop_net_redis";then
    docker volume create mwop_net_redis
fi

if ! docker volume ls | grep -q "mwop_net_shared_data";then
    docker volume create mwop_net_shared_data
fi
