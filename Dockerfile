# Use official PHP image with Apache server
FROM php:8.1-apache

# Install dependencies for PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files into the Apache server's document root
COPY . /var/www/html/

# Expose the default Apache port
EXPOSE 80
