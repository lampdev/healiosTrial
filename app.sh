#!/usr/bin/env bash

# chmod +x ./app.sh

set -e

BUILD_OPTION=$1

cp ./auth/.env.example ./auth/.env
cp ./crud/.env.example ./crud/.env
cp ./docker/.env.example ./docker/.env
cp ./gateway/.env.example ./gateway/.env

cd ./docker

docker-compose down -v --remove-orphans

if [ "$BUILD_OPTION" = "build" ]; then
    docker-compose build
fi

docker-compose up -d

export COMPOSER_ALLOW_SUPERUSER=1
cd ../auth; composer install
cd ../crud; composer install
cd ../gateway; composer install
cd ../docker;

docker-compose exec gateway php bin/phpunit ./tests/Feature/AuthApiTests.php
docker-compose exec gateway php bin/phpunit ./tests/Feature/UsersApiTests.php
