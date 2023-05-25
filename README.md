
# Terra Tritium Server Core

## Requirements

    MariaDB 10.7.4
    Laravel 9
    PHP 8.1.11

## Start local database

    create database origin

## Run data migrations

    php artisan migrate
    php artisan db:seed

## Start server service
  
    php artisan serve

### Listen schedule and queue
```
php artisan schedule:work
php artisan queue:work
```

## Procedure to update the database (run every time there is a database change)
  
    drop database origin
    create database origin
    php artisan migrate
    php artisan db:seed

## Test email service

    Site: https://mailtrap.io/
    Login: ronielvb@hotmail.com
    Senha: Tritium.2023#.

    Endereço     SMTP           :   sandbox.smtp.mailtrap.io
    Usuário da Aplicação    :   d1f0c36e3afcb4
    Senha                   :   f00ca9854c239b

    Url do site para o link dos templates de email
    ENV_URL_SITE=http://localhost:3000