# Healios Trial Task

## How to run the app

- `git clone https://github.com/lampdev/healiosTrial.git`
- `cd healiosTrial`
- `chmod +x ./app.sh`
- `./app.sh`

This script will build everything by using `.example` env files.

Another option is:
- create env files in `docker`, `crud`, `auth` and `gateway` folders using examples
- `cd ./docker`
- `docker-compose build baseimg`
- `docker-compose build`
- `docker-compose up -d`

## How to test the app

It can be done manually by sending following requests using Postman.

Login:
```
Method: POST
URL: 127.0.0.1/api/login
Body (raw JSON):
{
    "email": "admin@email.com",
    "password": "password"
}

To login as non-admin user:
{
    "email": "user@email.com",
    "password": "password"
}
```

Register:
```
Method: POST
URL: 127.0.0.1/api/register
Body:
{
    "name": "test",
    "email": "test@email.com",
    "password": "aqswdefr54535251"
}
```

Store user:
```
Method: POST
URL: 127.0.0.1/api/users/store
Body:
{
    "name": "test",
    "email": "test@email.com",
    "password": "aqswdefr54535251",
    "role_id": 2
}
Bearer Token: required
```

Show user:
```
Method: GET
URL: 127.0.0.1/api/users/show/{id}
Bearer Token: required
```

Update user:
```
Method: PUT
URL: 127.0.0.1/api/users/update/{id}
Body:
{
    "name": "test1",
    "email": "test1@email.com",
    "password": "aqswdefr545352511111",
    "role_id": 1
}
Bearer Token: required
```

Delete user:
```
Method: DELETE
URL: 127.0.0.1/api/users/delete/{id}
Bearer Token: required
```

Also, there is an opportunity to run Unit and Feature tests this way:
- unit
```
docker-compose exec crud ./vendor/bin/phpunit ./vendor/stas-lampdev/healios-trial-shared-services/tests
docker-compose exec crud ./vendor/bin/phpunit ./tests
docker-compose exec gateway php bin/phpunit ./tests/Unit/UserRetrieverTest.php
```
- feature
```
docker-compose exec gateway php bin/phpunit ./tests/Feature/AuthApiTests.php
docker-compose exec gateway php bin/phpunit ./tests/Feature/UsersApiTests.php
docker-compose exec gateway php bin/phpunit ./tests/Feature/AdminsApiTests.php
```

## Additional
- DB access: http://127.0.0.1:8080/
- default credentials: `homestead` / `secret`
