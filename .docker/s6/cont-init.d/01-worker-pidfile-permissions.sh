#!/bin/bash

set -e

echo "Setting up PID file directory..."
mkdir -p /var/run/supervisord
chown -R zendphp /var/run/supervisord
