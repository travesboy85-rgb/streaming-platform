#!/bin/sh

# Wait for Postgres to be ready before running migrations
until php artisan migrate --force; do
  echo "Postgres not ready yet, retrying in 5s..."
  sleep 5
done

# Seed demo accounts
php artisan db:seed --force

# Finally start Apache
exec apache2-foreground
