# Установка
* `docker-compose up -d`

* `docker-compose exec db bash`
* `psql -U postgres`
* `create database div;`
* `create database div_test;`
* `exit`

* `docker-compose exec nginx bash`
* `cd /etc/nginx/sites-enabled/`
* `ln -s ../sites-available/default .`
* `nginx -t`
* `nginx -s reload`
* `exit`

* `docker-compose exec php bash`
* `cd /var/www/html/app`
* `composer install`
* `php yii migrate -y`
* `exit`

* Сделать копию файла в корне `.env.example`, назвать `.env`
* Сделать копию файла в `app` `.env.example`, назвать `.env`
