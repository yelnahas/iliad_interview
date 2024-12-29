#!/bin/sh
set -e  # Exit immediately if a command exits with a non-zero status

# Install PHP dependencies using Composer
composer install  

# Check if the SQLite database file does not exist
if [ ! -f ./database/database.sqlite ]; then
    # Create the SQLite database file
    touch ./database/database.sqlite
fi

# Set proper permissions for the database file
chmod 666 ./database/database.sqlite  

# Copy the example environment file to .env
cp .env.example .env  

# Generate a new application key
php artisan key:generate  

# Run database migrations (force to bypass confirmation)
php artisan migrate --force  

# Start PHP-FPM
exec php-fpm