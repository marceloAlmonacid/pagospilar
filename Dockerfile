# Extiende la imagen PHP 7.3 con Apache
FROM php:7.3-apache

# Habilita la extensión mysqli
RUN docker-php-ext-install mysqli

# Configura Apache para que reconozca 'ServerName localhost'
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copia el contenido del proyecto al directorio de Apache
COPY . /var/www/html/

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Exponer el puerto 80
EXPOSE 80

# Comando para ejecutar Apache en primer plano
CMD ["apache2-foreground"]