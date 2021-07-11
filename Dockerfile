FROM php:7.4-apache
WORKDIR /var/www/html
COPY . /var/www/html/
RUN a2enmod rewrite
RUN chmod -R a+r /var/www/html/
EXPOSE 8000