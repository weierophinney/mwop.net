#!/bin/bash

set -e

export COMPOSER_HOME=/var/local/composer

echo "Starting application in ${PWD}"
echo "Composer version: $(/usr/local/sbin/composer --version)"
