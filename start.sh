#!/bin/sh

# Background process: wait for Postgres and run migrations + seeders
(
  until php -r "
  try {
      new PDO(
          'pgsql:host=' . getenv('DB_HOST') .
          ';port=' . getenv('DB_PORT') .
          ';dbname=' . getenv('DB_DATABASE') .
          ';sslmode=' . getenv('DB_SSLMODE'),
          getenv('DB_USERNAME'),
          getenv('DB_PASSWORD')
      );
      echo 'Postgres is ready';
  } catch (Exception \$e) {
      echo 'Postgres not ready yet, retrying in 5s...';
      exit(1);
  }
  "; do
    sleep 5
  done

  echo 'Running migrations...'
  php artisan migrate --force

  echo 'Seeding demo accounts...'
  php artisan db:seed --class=DemoAccountsSeeder --force
) &

# ✅ Start Apache immediately so Render detects port 80
exec apache2-foreground




