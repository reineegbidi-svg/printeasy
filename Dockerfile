FROM composer:2 AS composer

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

# Copier le code source avec les bonnes permissions
COPY --chown=www-data:www-data . .

# Copier Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir les variables pour Composer (désactiver les advisories)
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_DISABLE_ADVISORIES_CHECK=1

# Installer les dépendances (en mode production)
RUN composer update --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Définir la racine Apache sur le dossier public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf

# Fixer les permissions pour Laravel
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Exposer le port 80 (Apache utilise 80 par défaut)
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]
