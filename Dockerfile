# Use the official PHP image as a base
FROM php:8.0-apache

# Enable Apache mod_rewrite for clean URLs (if needed)
RUN a2enmod rewrite

# Install the required libraries to build PDO MySQL extension
RUN apt-get update && apt-get install -y \
    libmysqlclient-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PDO and PDO MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory inside the container
WORKDIR /var/www/html

# Copy the content of your project into the container
COPY . /var/www/html/

# Expose port 80 (default for HTTP)
EXPOSE 80

# Set the command to run the Apache web server in the foreground
CMD ["apache2-foreground"]
