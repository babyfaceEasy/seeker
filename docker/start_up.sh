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

php arisan key:generate
php artisan jwt:secret
php artisan config:cache