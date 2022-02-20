#!/bin/bash

set -e

if ! docker volume ls | grep -q "mwop_net_redis";then
    docker volume create mwop_net_redis
fi

if [ -f /mnt/art/photos.db ];then
    cp /mnt/art/photos.db data/photos.db
fi
