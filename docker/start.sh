#!/bin/bash
# ----------------------------------------------------------------------
# Run Composer
# ----------------------------------------------------------------------

cd /var/www
composer install --no-interaction
composer dump-autoload -o --no-interaction

# ----------------------------------------------------------------------
# Chmod
# ----------------------------------------------------------------------

chmod -R 777 storage
chmod -R 777 /var/www/storage/framework/cache
chmod -R 777 /var/www/storage/framework/views


# ----------------------------------------------------------------------
# Run Laravel Migrations
# ----------------------------------------------------------------------

php artisan migrate --force

# ----------------------------------------------------------------------
# Generate Keys and clear cache
# ----------------------------------------------------------------------

#php artisan passport:install
php artisan passport:keys
php artisan key:generate --force
#php artisan config:clear
#php artisan config:cache

# ----------------------------------------------------------------------
# Generate secret for auth.
# ----------------------------------------------------------------------
#php artisan jwt:secret
#php artisan clear-compiled && php artisan optimize

# ----------------------------------------------------------------------
# Generate swagger Documentation.
# ----------------------------------------------------------------------

#php artisan l5-swagger:generate

# ----------------------------------------------------------------------
# Start supervisord
# ----------------------------------------------------------------------

exec /usr/bin/supervisord -n -c /etc/supervisord.conf