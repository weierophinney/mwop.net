#!/bin/bash
set -e

echo "Setting permissions for output files..."
chown --dereference zendphp /dev/stdout /dev/stderr
echo "Permissions properly set."
