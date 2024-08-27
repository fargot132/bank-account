#!/bin/bash
set -e

if [[ -z "$*" ]]; then

  if [[ -f "composer.json" ]]; then
    echo "Installing dependencies"
    composer install

  fi

  if [[ -f "bin/console" ]]; then
      if grep -q '"doctrine/doctrine-migrations-bundle"' composer.lock; then
        echo "Running migrations"
        php bin/console doctrine:migrations:migrate --no-interaction || true
      else
        echo "Doctrine Migrations Bundle not found, skipping migrations"
      fi
    fi

  echo "Running PHP-FPM"
  exec php-fpm
else
  exec "$@"
fi
