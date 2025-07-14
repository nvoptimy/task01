FROM php:8.3-fpm

RUN apt-get update && apt-get install -y nginx && docker-php-ext-install mysqli pdo pdo_mysql && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY index.php /var/www/html/index.php
COPY nginx.conf /etc/nginx/sites-available/default

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80

CMD ["/bin/sh", "-c", "env|grep APP_ && service nginx start && php-fpm"]
