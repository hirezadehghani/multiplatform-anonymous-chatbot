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
    
    COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
    
    # Install dependencies first (better layer caching)
    COPY composer.json composer.lock ./
    RUN composer config -g repos.packagist composer https://package-mirror.liara.ir/repository/composer/ \
        && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts
    
    # Copy application code
    COPY . .
    
    # Create writable dirs excluded by .dockerignore
    RUN mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache \
        && composer dump-autoload --optimize \
        && php artisan package:discover --ansi || true \
        && chown -R www-data:www-data storage bootstrap/cache
    
    COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
    RUN chmod +x /usr/local/bin/entrypoint.sh
    
    EXPOSE 9000
    ENTRYPOINT ["entrypoint.sh"]
    CMD ["php-fpm"]