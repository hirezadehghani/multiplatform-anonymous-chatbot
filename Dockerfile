# -------- PHP BASE --------
FROM php:8.4-fpm

# ------- mirrors -------
RUN rm -f /etc/apt/sources.list.d/debian.sources
COPY docker/sources.list /etc/apt/sources.list

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libzip-dev libicu-dev \
    && docker-php-ext-install \
    pdo pdo_pgsql zip intl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# -------- COPY PROJECT --------
COPY . .

# -------- COMPOSER INSTALL --------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_DISABLE_NETWORK=0
ENV COMPOSER_PROCESS_TIMEOUT=2000

RUN composer config -g repos.packagist composer https://repo.packagist.org

# permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000

CMD sh -c "composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader && php-fpm"
