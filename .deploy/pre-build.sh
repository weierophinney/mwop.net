#!/bin/bash

if docker volume ls | grep -q "mwop_net_redis";then
    docker volume create mwop_net_redis
fi
