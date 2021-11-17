#!/bin/bash
set -e

echo "Setting permissions for data directory..."
chown -R zendphp:zendphp /var/www/data
echo "Permissions properly set."
