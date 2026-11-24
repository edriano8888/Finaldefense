FROM php:8.1-apache

# Install system dependencies & PHP extensions needed by Instroom (CodeIgniter 3 + PHPMailer + Apify/YouTube + XLSX)
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
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Allow .htaccess overrides for CodeIgniter 3 clean URLs
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . /var/www/html/

# Install PHP vendor dependencies if composer.json exists (skip dev scripts)
RUN if [ -f composer.json ]; then COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-scripts --optimize-autoloader --no-interaction; fi

# Ensure cache, logs, and uploads directories exist and are writable by Apache
RUN mkdir -p /var/www/html/application/cache /var/www/html/application/logs /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/application/cache /var/www/html/application/logs /var/www/html/uploads \
    && chmod -R 775 /var/www/html/application/cache /var/www/html/application/logs /var/www/html/uploads

# Dynamically bind Apache to Railway's injected $PORT and run auto-migration + seeder on startup
CMD ["sh", "-c", "PORT=${PORT:-80}; sed -i \"s/Listen 80/Listen ${PORT}/g\" /etc/apache2/ports.conf; sed -i \"s/:80/:${PORT}/g\" /etc/apache2/sites-available/000-default.conf; php /var/www/html/migrate_database.php || true; php /var/www/html/seed_database.php || true; apache2-foreground"]
