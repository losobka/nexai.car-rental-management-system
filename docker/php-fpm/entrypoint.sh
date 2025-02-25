#!/bin/sh

set -e

if [ ! -d vendor ];
then
  composer install --no-progress --no-interaction --dev --no-scripts
fi

ATTEMPTS_LEFT_TO_REACH_DATABASE=60

until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || php bin/console dbal:run-sql -q "SELECT 1" 2>&1; do
    if [ "$?" -eq 0 ]; then
        ATTEMPTS_LEFT_TO_REACH_DATABASE=0
        break
    fi

    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))

    echo "[NOTICE] Still waiting for database to be ready... Or maybe the database is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
done

[ "$DROP_DATABASE" ] && bin/console doctrine:database:drop --if-exists --force
[ "$CREATE_DATABASE" ] && bin/console doctrine:database:create
[ "$CREATE_SCHEMA" ] && bin/console doctrine:schema:create
[ "$LOAD_FIXTURES" ] && bin/console doctrine:fixtures:load --no-interaction --append

php-fpm -R