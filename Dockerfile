# Use official PHP image with Apache server
FROM php:8.1-apache

# Install dependencies for PostgreSQL and other necessary libraries
RUN apt-get update && apt-get install -y libpq-dev

# Install PDO and PDO_PGSQL PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files into the Apache server's document root
COPY . /var/www/html/

# Expose the default Apache port
EXPOSE 80
