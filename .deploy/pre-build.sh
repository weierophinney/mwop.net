#!/bin/bash

VOLUMES=$(docker volume ls)
if [[ ! "${VOLUMES}" =~ "mwop_net_redis" ]];then
    docker volume create mwop_net_redis
fi
