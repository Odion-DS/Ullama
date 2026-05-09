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

RUN mkdir -p /var/lib/nginx/tmp/client_body \
    && chown -R www-data:www-data /var/lib/nginx \
    && chmod -R 755 /var/lib/nginx

# Copy nginx configuration
COPY docker/image/nginx.conf /etc/nginx/nginx.conf
COPY docker/image/default.conf /etc/nginx/http.d/default.conf

# Copy supervisor configuration
COPY docker/image/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# PHP limits for large/long Ollama requests
RUN cat > /usr/local/etc/php/conf.d/ullama-limits.ini <<'EOF'
post_max_size=50M
upload_max_filesize=50M
max_execution_time=300
max_input_time=300
memory_limit=512M
default_socket_timeout=300
EOF


# Create log directories
RUN mkdir -p /var/log/supervisor

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
