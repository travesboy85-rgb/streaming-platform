#!/bin/sh

# Wait until Postgres is ready to accept connections
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME"; do
  echo "Postgres not ready yet, retrying in 5s..."
  sleep 5
done

# Run migrations and seed demo accounts
php artisan migrate --force
php artisan db:seed --force

# Finally start Apache
exec apache2-foreground

