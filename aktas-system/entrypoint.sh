#!/bin/sh
set -e

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
