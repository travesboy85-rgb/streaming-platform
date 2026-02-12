#!/bin/sh

# Wait until Laravel can connect to Postgres
until php -r "
try {
    new PDO(
        'pgsql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE') . ';sslmode=' . getenv('DB_SSLMODE'),
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

# Run migrations and seed demo accounts
php artisan migrate --force
php artisan db:seed --force

# Finally start Apache
exec apache2-foreground


