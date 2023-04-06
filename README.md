
# Terra Tritium Server Core

## Requisitos

    MariaDB 10.7.4
    Laravel 9
    PHP 8.1.11

## Iniciar banco de dados local

    create database origin

## Rodar a migração de dados

    php artisan migrate
    php artisan db:seed

## Iniciar o servidor backend
  
    php artisan serve

## Procedimento para atualizar a base de dados (rodar toda vez que existir alteração de banco)
  
    drop database origin
    create database origin
    php artisan migrate
    php artisan db:seed