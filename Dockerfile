FROM php:8.1-cli

# Install system dependencies & PHP extensions needed by Instroom
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql zip gd curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html/

# Install PHP vendor dependencies (skip dev scripts)
RUN if [ -f composer.json ]; then COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-scripts --optimize-autoloader --no-interaction; fi

# Ensure cache, logs, and uploads directories exist and are writable
RUN mkdir -p /var/www/html/application/cache /var/www/html/application/logs /var/www/html/uploads \
    && chmod -R 777 /var/www/html/application/cache /var/www/html/application/logs /var/www/html/uploads

# Run migration + seeder, then start PHP server on Railway's exact $PORT using server-router.php
CMD ["sh", "-c", "php /var/www/html/migrate_database.php || true; php /var/www/html/seed_database.php || true; exec php -S 0.0.0.0:${PORT:-8080} -t /var/www/html /var/www/html/server-router.php"]
