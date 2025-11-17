FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    curl

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_sqlite mbstring gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set Apache document root
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Create database file
RUN mkdir -p database && touch database/database.sqlite

# FIX FOR SESSION ISSUE:
RUN mkdir -p storage/framework/sessions
RUN chmod -R 775 storage/framework/sessions
RUN chown -R www-data:www-data storage/framework/sessions
# Add this line after creating the database file
RUN php artisan migrate --force
# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
