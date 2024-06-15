# Extiende la imagen PHP 7.3 con Apache
FROM php:7.3-apache

# Habilita la extensión mysqli
RUN docker-php-ext-install mysqli
