#!/bin/bash

# Run database migrations automatically
echo "Running database migrations..."
php artisan migrate --force

# Start the web server (inheriting the base image default command)
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf