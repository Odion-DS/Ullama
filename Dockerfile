FROM php:8.5-fpm-alpine

LABEL maintainer="Ullama"

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libzip-dev \
    icu-dev \
    zip \
    unzip \
    supervisor \
    nginx

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Copy nginx configuration
COPY docker/image/nginx.conf /etc/nginx/nginx.conf
COPY docker/image/default.conf /etc/nginx/http.d/default.conf

# Copy supervisor configuration
COPY docker/image/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create log directories
RUN mkdir -p /var/log/supervisor

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
