#!/bin/bash

set -e

if ! docker volume ls | grep -q "mwop_net_redis";then
    docker volume create mwop_net_redis
fi

if ! docker volume ls | grep -q "mwop_net_photodb";then
    docker volume create mwop_net_photodb
fi
