FROM php:8.2-apache

# Instalamos extensiones de base de datos
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitamos mod_rewrite para Apache
RUN a2enmod rewrite

# Ajustamos permisos
RUN chown -R www-data:www-data /var/www/html