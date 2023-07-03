
# Terra Tritium Server Core

## Requirements

    MariaDB 10.7.4
    Laravel 10
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

## SWAGGER

    1- Adicionar Notation no metodo da controller
    Ex :            
         * @OA\Get (
         *      path="/api/country/list",
         *      summary="List of countries",
         *      tags={"Contries"},
         *      description="List of countries",
         * @OA\Parameter(
         *          id="id",
         *          description="country id",
         *          required=true,
         *          in="path",
         *          @OA\Schema(
         *              type="integer"
         *          )
         *      ),
         * @OA\Response(response="200", description="Sucesso")
         * )
         */

        [tags é o agrupador dos endpoints]

    # Se for um endpoint protegido pelo middleware é necessario adicionar as informaçoes de security na annotation
    
    Ex:
     * @OA\Get (
     *     path="/api/player/show",
     *     tags={"Players"},
     *     summary="List Players",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(response="200", description="Resposta bem-sucedida")
     * )
     *  @OA\SecurityScheme(
     *     type="http",
     *     scheme="bearer",
     *     securityScheme="bearerAuth"
     * )

    Siga as etapas abaixo para adicionar o token de autenticação no Swagger UI:
    
     - Abra o Swagger UI no navegador, geralmente acessível por meio de /api/documentation .
    
     - Procure pelo botão "Authorize" no canto superior direito da página. Clique nele para abrir um campo de texto para inserir o token.
    
     - Insira o token no campo de texto (Pode ser adiquirido no endpoint generate-token).
        - Resposta do Endpoint :
                {
                    "message": "Token generated successfully",
                    "token": "4|T0VrwehkynvhxhzNrBbXrxk8crOuk5wnEZb9hZbY"
                }
    
     - Clique no botão "Authorize" para salvar o token.

     - Agora é só consultar o endpoint que deseja
    
    
    2- Após escrever a annotation, rodar o comando para gerar a doc no swagger
        - php artisan l5-swagger:generate

    3- Acessar o Swagger local
        http://localhost:8000/api/documentation

## DOCKER CONTAINER

    1 - Baixar e construir as imagens

        docker-compose build 
        
    2 - Iniciar os containers em modo background

        docker-composer up -d

    3 - Listar os container em execução

        docker ps 

    4 - Acessar o container para executar comandos com php

        docker-composer exec -it app bash
    
    Tem uma imagem para instalar as dependências
