FROM php:8.2-apache

# Instalamos extensiones de base de datos
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Instalamos el cliente de MySQL para poder usarlo desde la línea de comandos
RUN apt-get update && apt-get install -y default-mysql-client mariadb-client

# Habilitamos mod_rewrite para Apache
RUN a2enmod rewrite

# Ajustamos permisos
RUN chown -R www-data:www-data /var/www/html