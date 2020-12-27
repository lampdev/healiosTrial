#!/usr/bin/env bash

# chmod +x ./app.sh

set -e

BUILD_OPTION=$1

cp ./auth/.env.example ./auth/.env
cp ./crud/.env.example ./crud/.env
cp ./docker/.env.example ./docker/.env

cd ./docker

docker-compose down -v --remove-orphans

if [ "$BUILD_OPTION" = "build" ]; then
    docker-compose build
fi

docker-compose up -d

export COMPOSER_ALLOW_SUPERUSER=1
cd ../auth; composer install
cd ../crud; composer install
cd ..
