#!/bin/sh
set -e

# Normalize the container to a configurable runtime.
if [ ! -f .env ]; then
  cp .env.example .env
fi

: "${DB_CONNECTION:=mysql}"
: "${DB_HOST:=mysql}"
: "${DB_PORT:=3306}"
: "${DB_DATABASE:=aktas_system}"
: "${DB_USERNAME:=root}"
: "${DB_PASSWORD:=secret}"
: "${SESSION_DRIVER:=database}"
: "${CACHE_STORE:=database}"
: "${QUEUE_CONNECTION:=database}"

sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=${DB_CONNECTION}/" .env
sed -i "s/^DB_HOST=.*/DB_HOST=${DB_HOST}/" .env
sed -i "s/^DB_PORT=.*/DB_PORT=${DB_PORT}/" .env
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=${DB_DATABASE}/" .env
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=${DB_USERNAME}/" .env
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env
sed -i "s/^SESSION_DRIVER=.*/SESSION_DRIVER=${SESSION_DRIVER}/" .env
sed -i "s/^CACHE_STORE=.*/CACHE_STORE=${CACHE_STORE}/" .env
sed -i "s/^QUEUE_CONNECTION=.*/QUEUE_CONNECTION=${QUEUE_CONNECTION}/" .env

if [ "${DB_CONNECTION}" = "sqlite" ]; then
  sed -i 's#^DB_DATABASE=.*#DB_DATABASE=/var/www/html/database/database.sqlite#' .env
  mkdir -p database
  touch database/database.sqlite
fi

# Retry migrations a few times to allow DB to become ready
RETRIES=6
COUNT=0
until /usr/local/bin/php artisan migrate --force --seed
do
  COUNT=$((COUNT+1))
  if [ $COUNT -ge $RETRIES ]; then
    echo "Migrations failed after $COUNT attempts, continuing to start Apache."
    break
  fi
  echo "Migration attempt $COUNT failed; retrying in 5s..."
  sleep 5
done

# Start Apache in foreground
exec apache2-foreground
