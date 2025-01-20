#!/bin/sh

set -e

ATTEMPTS_LEFT_TO_REACH_DATABASE=60

until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || DATABASE_ERROR=$(php bin/console dbal:run-sql -q "SELECT 1" 2>&1); do
    if [ $? -eq 0 ]; then
        ATTEMPTS_LEFT_TO_REACH_DATABASE=0
        break
    fi

    sleep 1
    ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE - 1))

    echo "[NOTICE] Still waiting for database to be ready... Or maybe the database is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left."
done

[ $DROP_DATABASE == true ] && bin/console doctrine:database:drop --if-exists --force
[ $CREATE_DATABASE == true ] && bin/console doctrine:database:create
[ $CREATE_SCHEMA == true ] && bin/console doctrine:schema:create
[ $LOAD_FIXTURES == true ] && bin/console doctrine:fixtures:load --no-interaction --append

php-fpm -R