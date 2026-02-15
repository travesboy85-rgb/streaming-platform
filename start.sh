#!/bin/sh

# Background process: wait for Postgres and run migrations
(
  until pg_isready -h $DB_HOST -p $DB_PORT -U $DB_USERNAME; do
    echo "Postgres not ready yet, retrying in 5s..."
    sleep 5
  done

  echo "✅ Postgres is ready!"
  php artisan migrate --force

  if [ "$APP_ENV" != "production" ]; then
    echo "🌱 Seeding demo accounts..."
    php artisan db:seed --class=DemoAccountsSeeder --force
  fi
) &

# ✅ Start Apache immediately so Render detects port 80
exec apache2-foreground








