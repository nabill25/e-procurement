# Multi-stage build for PHP CodeIgniter app
FROM php:7.4-apache AS base

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libmariadb-dev-compat \
    mariadb-client \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql \
    && docker-php-ext-enable mysqli pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy .htaccess for Apache
COPY .htaccess /var/www/html/.htaccess

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
