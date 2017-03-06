FROM php:7.1-apache

RUN apt-get update \
 && apt-get install -y zlib1g-dev \
 && docker-php-ext-install zip \
 && a2enmod rewrite \
 && a2enmod headers \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/000-default.conf \
 && mv /var/www/html /var/www/public \
 && echo "AllowEncodedSlashes On" >> /etc/apache2/apache2.conf

WORKDIR /var/www
