# FROM php:8.3-fpm-alpine

# RUN apk add --no-cache \
#     libzip \
#     zip \
#     icu \
#     oniguruma \
#     bash \
#     supervisor \
#     && apk add --no-cache --virtual .build-deps \
#     libzip-dev \
#     icu-dev \
#     oniguruma-dev \
#     $PHPIZE_DEPS \
#     && docker-php-ext-install \
#     zip \
#     intl \
#     pdo_mysql \
#     && apk del .build-deps

# # Installer Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Configurer le dossier de travail
# WORKDIR /var/www

# # Copier l'application
# COPY . .

# # Installer les dépendances PHP
# RUN composer install --optimize-autoloader --no-interaction

# # Créer un utilisateur non-root
# RUN addgroup -g 1000 symfony && \
#     adduser -u 1000 -G symfony -D symfony && \
#     chown -R symfony:symfony /var/www

# USER symfony

# # Exposer les ports
# EXPOSE 8000
# EXPOSE 8080 

# # Commande de démarrage (serveur web + websocket)
# CMD ["sh", "-c", "php-fpm & php /var/www/bin/ws-server.php"]





FROM php:8.3-fpm-alpine

# Installer les dépendances système + extensions PHP nécessaires
RUN apk add --no-cache \
    libzip \
    zip \
    icu \
    oniguruma \
    bash \
    supervisor \
    openssl \
    && apk add --no-cache --virtual .build-deps \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    openssl-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-install \
    zip \
    intl \
    pdo_mysql \
    opcache \
    && apk del .build-deps

# Configuration OPcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.revalidate_freq=60" >> /usr/local/etc/php/conf.d/opcache.ini && \
    echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini
# Activer OpenSSL (souvent déjà activé avec php-fpm, mais on force)
RUN docker-php-ext-enable openssl || true

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurer le dossier de travail
WORKDIR /var/www

# Copier l'application
COPY . .

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-interaction
# RUN composer install --prefer-dist --no-interaction

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
