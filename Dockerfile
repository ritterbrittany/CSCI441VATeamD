# Use the official PHP image as a base
FROM php:8.0-apache

# Enable Apache mod_rewrite for clean URLs (if needed)
RUN a2enmod rewrite

# Set working directory inside the container
WORKDIR /var/www/html

# Copy the content of your project into the container
COPY . /var/www/html/

# Install any additional dependencies (if needed)
RUN docker-php-ext-install pdo pdo_mysql

# Expose port 80 (default for HTTP)
EXPOSE 80

# Set the command to run the Apache web server in the foreground
CMD ["apache2-foreground"]git