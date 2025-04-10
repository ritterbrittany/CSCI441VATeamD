# Use the official PHP image as a base
FROM php:8.0-apache

# Install system dependencies for MySQL (if using MySQL)
RUN apt-get update && apt-get install -y libmysqlclient-dev

# Install PHP extensions for MySQL (PDO and PDO_MYSQL)
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite for clean URLs (if needed)
RUN a2enmod rewrite

# Set working directory inside the container
WORKDIR /var/www/html

# Copy the content of your project into the container
COPY . /var/www/html/

# Expose port 80 (default for HTTP)
EXPOSE 80

# Set the command to run the Apache web server in the foreground
CMD ["apache2-foreground"]