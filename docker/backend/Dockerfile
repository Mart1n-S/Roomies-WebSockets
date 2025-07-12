FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    libzip \
    zip \
    icu \
    oniguruma \
    bash \
    supervisor \
    && apk add --no-cache --virtual .build-deps \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
    zip \
    intl \
    pdo_mysql \
    && apk del .build-deps

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer le dossier de travail
WORKDIR /var/www

# Copier l'application
COPY ../../backend/ ./

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-interaction

# Créer un utilisateur non-root
RUN addgroup -g 1000 symfony && \
    adduser -u 1000 -G symfony -D symfony && \
    chown -R symfony:symfony /var/www

USER symfony

# Exposer les ports
EXPOSE 8000
EXPOSE 8080 

# Commande de démarrage (serveur web + websocket)
CMD ["sh", "-c", "php-fpm & php /var/www/bin/ws-server.php"]