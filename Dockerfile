FROM php:7.4-apache
#Install PHP extensions
RUN docker-php-ext-install mysqli
COPY --chown=www-data:www-data ./web/ /var/www/html
EXPOSE 80
