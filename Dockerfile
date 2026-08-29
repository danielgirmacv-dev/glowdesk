FROM php:8.2-alpine

# Install system dependencies, PHP extensions (including gd for Excel/phpspreadsheet), and Node.js
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libxml2-dev \
    libzip-dev \
    nodejs \
    npm \
    sqlite-dev \
    sqlite \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite pdo_mysql bcmath mbstring zip gd

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source code
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build Vite assets
RUN npm install && npm run build

# Setup SQLite database and permissions
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views database \
    && touch database/database.sqlite \
    && chmod -R 777 storage bootstrap/cache database

EXPOSE 8000

# Start command
CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
