#!/usr/bin/env bash

# chmod +x ./app.sh

set -e

BUILD_OPTION=$1

cp ./auth/.env.example ./auth/.env
cp ./crud/.env.example ./crud/.env
cp ./docker/.env.example ./docker/.env
cp ./gateway/.env.example ./gateway/.env

cp ./docker/images/base/config/auth.json.example ./docker/images/base/config/auth.json
cp ./docker/images/base/config/keys.dev.pub.example ./docker/images/base/config/keys.dev.pub
cp ./docker/images/base/config/keys.tags.pub.example ./docker/images/base/config/keys.tags.pub

cd ./docker

docker-compose down -v --remove-orphans

if [ "$BUILD_OPTION" = "build" ]; then
    rm -rf ../auth/vendor
    rm -rf ../auth/var
    rm -rf ../crud/vendor
    rm -rf ../crud/var
    rm -rf ../gateway/vendor
    rm -rf ../gateway/var
    docker-compose build baseimg
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
docker-compose exec gateway php bin/phpunit ./tests/Feature/AdminsApiTests.php
