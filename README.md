
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

## Acesso a caixa de e-mail para teste

    Site: https://mailtrap.io/
    Login: ronielvb@hotmail.com
    Senha: Tritium.2023#.

    Endereço     SMTP           :   sandbox.smtp.mailtrap.io
    Usuário da Aplicação    :   d1f0c36e3afcb4
    Senha                   :   f00ca9854c239b

    Url do site para o link dos templates de email
    ENV_URL_SITE=http://localhost:3000