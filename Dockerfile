FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Installer les extensions PHP nécessaires pour Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Activer mod_rewrite d'Apache
RUN a2enmod rewrite

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le code source
COPY --chown=www-data:www-data . .

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer Composer pour ignorer LES ADVISORIES !
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer config --no-interaction --local 'config.platform.php' '8.2'
RUN composer config --no-interaction --local 'allow-plugins.pestphp/pest-plugin' true
RUN composer config --no-interaction --local 'allow-plugins.php-http/discovery' true
RUN composer config --no-interaction --local 'advisories.block' false

# Installer les dépendances : update pour générer lock !
RUN composer update --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Définir la racine Apache sur le dossier public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Fixer les permissions pour Laravel
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port 80
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]
