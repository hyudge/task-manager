FROM php:8.2-apache

# Copier uniquement le frontend dans la racine web Apache
COPY frontend/ /var/www/html/

# Permissions correctes pour Apache
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Activer rewrite (utile si tu utilises des routes ou .htaccess)
RUN a2enmod rewrite

# S'assurer qu'Apache sert bien index.php / index.html
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Redémarrage Apache propre
EXPOSE 80