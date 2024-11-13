# Use an official PHP image with Apache
FROM php:8.2-apache

# Install PHP and Apache dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \
    git \
    libpq-dev \
    curl \
    && docker-php-ext-install intl pdo pdo_pgsql zip

# Enable Apache modules
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Set the working directory
WORKDIR /var/www/html

# Copy the current directory contents into the container
COPY . .

# Copy custom Apache configuration
COPY symfony.conf /etc/apache2/sites-available/000-default.conf

# Install Symfony dependencies
RUN composer install

# Install Tailwind CSS and other frontend dependencies
RUN npm install
# Set permissions for the Apache user
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/var

# Expose the port Apache will run on
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
