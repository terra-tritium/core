php artisan database:drop
mysqladmin -uroot -p123456 create origin
php artisan migrate
php artisan db:seed
