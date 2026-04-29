FROM php:8.2-apache

# Installer extensions utiles (MySQL notamment)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html/

# Donner les droits
RUN chown -R www-data:www-data /var/www/html

# Port utilisé
EXPOSE 80