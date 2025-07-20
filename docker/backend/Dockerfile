# Utilise l'image officielle PHP 8.3 FPM basée sur Alpine (légère)
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

# Copie Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définit le dossier de travail (tout sera exécuté depuis /var/www)
WORKDIR /var/www

# Copie le code de l’application dans le conteneur (à adapter selon l’arborescence réelle)
COPY ../../backend/ ./

# Installe les dépendances PHP (en mode production, sans interaction)
RUN composer install --optimize-autoloader --no-interaction

# Crée un utilisateur non-root nommé 'symfony' (bonne pratique de sécurité)
RUN addgroup -g 1000 symfony && \
    adduser -u 1000 -G symfony -D symfony && \
    chown -R symfony:symfony /var/www

# Utiliser un utilisateur non-root est une **bonne pratique sécurité** :
#   - Un process qui tourne en root dans un conteneur (surtout exposé au web) est risqué.
#   - Ici, on crée un user "symfony" qui possède les fichiers et n'a pas de privilèges élevés.
#   - Cela limite l'impact d'une faille de sécurité potentielle (on ne peut pas "rooter" le conteneur !).
USER symfony

# Expose les ports nécessaires
EXPOSE 8000
EXPOSE 8080

# Commande de démarrage : lance PHP-FPM ET le serveur WebSocket en parallèle
CMD ["sh", "-c", "php-fpm & php /var/www/bin/ws-server.php"]
