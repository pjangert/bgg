FROM php:7.4-apache
#Install PHP extensions
RUN docker-php-ext-install mysqli
COPY --chown=www-data:www-data ./web/ /var/www/html
COPY ./start_env /usr/local/bin
ENTRYPOINT ["/usr/local/bin/start_env","docker-php-entrypoint"]
CMD ["apache2-foreground"]
EXPOSE 80
