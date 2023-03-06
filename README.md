# PizzaProject

Customisez votre pizza

## Caracteristiques techniques
```
PHP 8.1
Symfony 6.2
Postgres 15
Volta
```

## Installation
```
docker-compose up
composer install
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load
curl https://get.volta.sh | bash
yarn install
yarn build
```
