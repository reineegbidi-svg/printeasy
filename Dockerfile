FROM serversideup/php:8.2-fpm-nginx

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers du projet
COPY . /var/www/html

# Installer Composer et les dépendances (ignorer TOUS les problèmes !)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer update --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Définir les permissions correctes (essentielles pour Laravel)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Exposer le port 8080 (utilisé par l'image serversideup)
EXPOSE 8080
