
#Iniciar os containers

    docker-compose build

    docker-compose up

#Executar comando abaixo na raiz do projeto após iniciar o container

   docker-compose exec -T app php /var/www/artisan migrate:fresh --seed --force