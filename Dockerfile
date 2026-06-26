# -------- PHP BASE --------
FROM php:8.4-fpm

# ------- mirrors -------
RUN rm -f /etc/apt/sources.list.d/debian.sources
COPY docker/sources.list /etc/apt/sources.list


Conversation with Gemini
REDIS_CLIENT=phpredis

REDIS_HOST=redis

REDIS_PASSWORD=null

REDIS_PORT=6379Dolor et ea dolor ne.



1:M 26 Jun 2026 08:06:15.955 * <search> Enabled workers threadpool of size 2

redis        | 1:M 26 Jun 2026 08:06:15.963 * <search> Subscribe to config changes

redis        | 1:M 26 Jun 2026 08:06:15.966 * <search> Subscribe to cluster slot migration events

redis        | 1:M 26 Jun 2026 08:06:15.966 * <search> Enabled role change notification

redis        | 1:M 26 Jun 2026 08:06:15.972 * <search> Cluster configuration: AUTO partitions, type: 0, coordinator timeout: 0ms

redis        | 1:M 26 Jun 2026 08:06:15.977 * Module 'search' loaded from /usr/local/lib/redis/modules//redisearch.so

redis        | 1:M 26 Jun 2026 08:06:15.982 * <timeseries> RedisTimeSeries version 80800, git_sha=42ca4f1078fca732b7f9256adbf25914d67e1cc9

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries> Redis version found by RedisTimeSeries : 8.8.0 - oss

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries> Registering configuration options: [

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries>      { ts-compaction-policy   :              }

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries>      { ts-num-threads         :            3 }

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries>      { ts-libmr-protocol      :     INTERNAL }

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries>      { ts-retention-policy    :            0 }

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries>      { ts-duplicate-policy    :        block }

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries>      { ts-chunk-size-bytes    :         4096 }

redis        | 1:M 26 Jun 2026 08:06:15.987 * <timeseries>      { ts-encoding            :   compressed }

redis        | 1:M 26 Jun 2026 08:06:15.988 * <timeseries>      { ts-ignore-max-time-diff:            0 }

redis        | 1:M 26 Jun 2026 08:06:15.988 * <timeseries>      { ts-ignore-max-val-diff :     0.000000 }

redis        | 1:M 26 Jun 2026 08:06:15.988 * <timeseries> ]

redis        | 1:M 26 Jun 2026 08:06:15.988 * <timeseries> Detected redis oss

redis        | 1:M 26 Jun 2026 08:06:15.989 * <timeseries> Subscribe to ASM events

redis        | 1:M 26 Jun 2026 08:06:15.989 * <timeseries> Enabled diskless replication

redis        | 1:M 26 Jun 2026 08:06:15.989 * Module 'timeseries' loaded from /usr/local/lib/redis/modules//redistimeseries.so

redis        | 1:M 26 Jun 2026 08:06:16.004 * <ReJSON> Created new data type 'ReJSON-RL'

redis        | 1:M 26 Jun 2026 08:06:16.006 * <ReJSON> version: 80800 git sha: unknown branch: unknown

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Exported RedisJSON_V1 API

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Exported RedisJSON_V2 API

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Exported RedisJSON_V3 API

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Exported RedisJSON_V4 API

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Exported RedisJSON_V5 API

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Exported RedisJSON_V6 API

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Exported RedisJSON_V7 API

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Enabled diskless replication

redis        | 1:M 26 Jun 2026 08:06:16.009 * <ReJSON> Initialized shared string cache, thread safe: true.

redis        | 1:M 26 Jun 2026 08:06:16.009 * Module 'ReJSON' loaded from /usr/local/lib/redis/modules//rejson.so

redis        | 1:M 26 Jun 2026 08:06:16.009 * <search> Acquired RedisJSON_V7 API

redis        | 1:M 26 Jun 2026 08:06:16.010 * Server initialized

redis        | 1:M 26 Jun 2026 08:06:16.010 * Ready to accept connections tcp

redis        | 1:M 26 Jun 2026 08:06:16.010 # WARNING: Redis does not require authentication and is not protected by network restrictions. Redis will accept connections from any IP address on any network interface.

scheduler    |   Class "Redis" not found

scheduler    | 

scheduler    |   at vendor/laravel/framework/src/Illuminate/Redis/Connectors/PhpRedisConnector.php:80

scheduler    |      76▕      * @throws \LogicException

scheduler    |      77▕      */

scheduler    |      78▕     protected function createClient(array $config)

scheduler    |      79▕     {

scheduler    |   ➜  80▕         return tap(new Redis, function ($client) use ($config) {

scheduler    |      81▕             if ($client instanceof RedisFacade) {

scheduler    |      82▕                 throw new LogicException(

scheduler    |      83▕                     extension_loaded('redis')

scheduler    |      84▕                         ? 'Please remove or rename the Redis facade alias in your "app" configuration file in order to avoid collision with the PHP Redis extension.'

The error you are seeing in your scheduler container stems from a mismatch between your configuration and the PHP environment.

Plaintext
Class "Redis" not found 
at vendor/laravel/framework/src/Illuminate/Redis/Connectors/PhpRedisConnector.php:80
Because you have set REDIS_CLIENT=phpredis, Laravel is attempting to use PHP's native, compiled C extension (phpredis) to connect to your Redis instance. The error Class "Redis" not found means the phpredis extension is not installed or enabled in the PHP environment running your scheduler container.

Here are the two ways to fix this, depending on how you want to manage your dependencies.

Solution 1: Install the phpredis Extension (Recommended)
If you want to keep using phpredis (which is generally faster and more performant), you need to update the Dockerfile or the environment setup for your PHP/Laravel containers to include the extension.

If your container is based on Ubuntu/Debian, add this to your Dockerfile:

Dockerfile
RUN apt-get update && apt-get install -y php-redis
If your container is based on the official PHP Alpine images, add this:

Dockerfile
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis
Note: Remember to rebuild your containers (docker compose up --build) after making changes to your Dockerfile.

Solution 2: Switch to the predis Client (Easier/Faster)
If you don't want to mess with compiling or installing PHP system extensions, you can switch to predis. predis is a pure PHP client package managed entirely via Composer.

Step 1: Change your .env file to use predis:

Code snippet
REDIS_CLIENT=predis
Step 2: Install the package via Composer inside your application container:

Bash
composer require predis/predis
A Quick Check
While your Redis container is healthy and ready (Ready to accept connections tcp), it also emitted a warning: Redis does not require authentication. If this environment is going anywhere near production later on, make sure to replace REDIS_PASSWORD=null with a secure password and update your Redis configuration accordingly!

Package php-redis is a virtual package provided by:

  php8.4-redis 6.2.0-1 [Not candidate version]



E: Package 'php-redis' has no installation candidate

Ah, the classic apt version trap.

Because you are using a newer version of PHP (likely PHP 8.4 or similar based on that output) and standard Debian/Ubuntu repositories can be a bit strict about package candidates, apt-get install php-redis is throwing a fit.

Since php-redis isn't cooperating as a quick apt package, you have two clear paths forward to get your container running.

Option 1: Use PECL to install it (Most Reliable for Docker)
Instead of relying on the OS package manager (apt), you can use PECL (the PHP Extension Community Library) to compile and install the extension directly for whatever version of PHP your Docker container is running.

Update the Dockerfile for your PHP/scheduler container to include these lines:

Dockerfile
# Install system dependencies required to compile the extension
RUN apt-get update && apt-get install -y \
    $PHPIZE_DEPS \
    libssl-dev \
    && pecl install redis \
    && docker-php-ext-enable redis

# Clean up apt caches to keep the image slim
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
Note: $PHPIZE_DEPS is a built-in shortcut variable in official PHP Docker images that includes tooling like gcc, make, and autoconf. If your base image doesn't recognize it, replace $PHPIZE_DEPS with build-essential autoconf.

Option 2: Fall back to predis (The No-Compile Route)
If you don't want to add weight or compile time to your Docker image, you can completely bypass apt and pecl by switching Laravel over to the pure-PHP Redis client.

Open your .env file and change the client line:

Code snippet
REDIS_CLIENT=predis
Run this command in your terminal (or inside your app container) to add the package via Composer:

Bash
composer require predis/predis
Both options will satisfy Laravel's scheduler. Option 1 gives you raw C performance, while Option 2 gets you up and running in about 15 seconds without modifying your Docker environment. Which approach fits your workflow better?

rewrite all the packages with option A



# -------- PHP BASE --------

FROM php:8.4-fpm



# ------- mirrors -------

RUN rm -f /etc/apt/sources.list.d/debian.sources

COPY docker/sources.list /etc/apt/sources.list



RUN apt-get update && apt-get install -y \

    git curl zip unzip php-redis\

    libpq-dev libzip-dev libicu-dev \

    && docker-php-ext-install \

    pdo pdo_pgsql zip intl \

    && rm -rf /var/lib/apt/lists/*

Here is your rewritten Dockerfile. I removed php-redis from the apt list and replaced it with the proper PECL installation and enablement steps so it builds cleanly on PHP 8.4:

Dockerfile
# -------- PHP BASE --------
FROM php:8.4-fpm

# ------- mirrors -------
RUN rm -f /etc/apt/sources.list.d/debian.sources
COPY docker/sources.list /etc/apt/sources.list

# Install system dependencies, compile phpredis via PECL, and install core extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# -------- COPY PROJECT --------
COPY . .

# -------- COMPOSER INSTALL --------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_DISABLE_NETWORK=0
ENV COMPOSER_PROCESS_TIMEOUT=2000

# permissions
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
# composer config -g repos.packagist composer https://package-mirror.liara.ir/repository/composer/ && composer install --no-dev --prefer-dist &&

CMD sh -c "php-fpm"
