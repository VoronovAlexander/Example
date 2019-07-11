composer install
php artisan migrate
php artisan db:seed
ln -s /home/lumen/storage/app /home/lumen/public/storage
php -S 0.0.0.0:8080 -t public