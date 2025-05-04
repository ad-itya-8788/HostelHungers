# Step 1: Use an official PHP image with Apache
FROM php:8.0-apache

# Step 2: Install dependencies for PostgreSQL and other PHP extensions
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pgsql pdo_pgsql

# Step 3: Enable Apache mod_rewrite (useful for pretty URLs)
RUN a2enmod rewrite

# Step 4: Set the working directory inside the container
WORKDIR /var/www/html

# Step 5: Copy your project files into the container's working directory
COPY . .

# Step 6: Expose the port for Apache to listen on
EXPOSE 80

# Step 7: Start Apache in the foreground
CMD ["apache2-foreground"]
