#!/bin/bash

# Create the /var/www/html/API folder if it doesn't exist
mkdir -p /var/www/html/API

# Create the symlink
ln -sf /var/www/api/public /var/www/html/API/v1

exec "$@"