# Use the official PHP image as the base
FROM php:7.4-apache

# Install PostgreSQL dependencies
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set the working directory in the container
WORKDIR /var/www/html

# Copy the local directory (your project) to the container
COPY . .

# Expose port 80 to access the app
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
