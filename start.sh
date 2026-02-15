#!/bin/sh

# Wait for Postgres to be ready
until pg_isready -h $DB_HOST -p $DB_PORT -U $DB_USERNAME; do
  echo "Postgres not ready yet, retrying in 5s..."
  sleep 5
done

echo "✅ Postgres is ready!"

# Run migrations safely
php artisan migrate --force

# Seed demo accounts only in non-production
if [ "$APP_ENV" != "production" ]; then
  echo "🌱 Seeding demo accounts..."
  php artisan db:seed --class=DemoAccountsSeeder --force
fi

# Start Apache so Render detects port 80
exec apache2-foreground







