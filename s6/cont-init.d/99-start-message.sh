#!/bin/bash

set -e

composer=${COMPOSER:-/usr/local/sbin/composer}
export COMPOSER_HOME=/var/local/composer

echo "Starting application in ${PWD}"
echo "Composer version: $("${composer}" --version)"
