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

# CREATE ESSENTIAL TABLES MANUALLY

# Users table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255) NOT NULL, email VARCHAR(255) UNIQUE NOT NULL, email_verified_at DATETIME NULL, password VARCHAR(255) NOT NULL, remember_token VARCHAR(100) NULL, created_at DATETIME NULL, updated_at DATETIME NULL, deleted_at DATETIME NULL);"

# Password resets table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS password_resets (email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, created_at DATETIME NULL);"

# Sessions table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS sessions (id VARCHAR(255) PRIMARY KEY, user_id INTEGER NULL, ip_address VARCHAR(45) NULL, user_agent TEXT NULL, payload TEXT NOT NULL, last_activity INTEGER NOT NULL);"

# Cache table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS cache (key VARCHAR(255) PRIMARY KEY, value TEXT NOT NULL, expiration INTEGER);"

# Jobs table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS jobs (id INTEGER PRIMARY KEY AUTOINCREMENT, queue VARCHAR(255) NOT NULL, payload TEXT NOT NULL, attempts INTEGER NOT NULL, reserved_at INTEGER NULL, available_at INTEGER NOT NULL, created_at INTEGER NOT NULL);"

# Failed jobs table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS failed_jobs (id INTEGER PRIMARY KEY AUTOINCREMENT, uuid VARCHAR(255) UNIQUE NOT NULL, connection TEXT NOT NULL, queue TEXT NOT NULL, payload TEXT NOT NULL, exception TEXT NOT NULL, failed_at DATETIME NOT NULL);"

# PERMISSION TABLES (for Spatie Laravel Permission)

# Roles table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS roles (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255) NOT NULL, guard_name VARCHAR(255) NOT NULL, created_at DATETIME NULL, updated_at DATETIME NULL);"

# Permissions table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS permissions (id INTEGER PRIMARY KEY AUTOINCREMENT, name VARCHAR(255) NOT NULL, guard_name VARCHAR(255) NOT NULL, created_at DATETIME NULL, updated_at DATETIME NULL);"

# Model has roles table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS model_has_roles (role_id INTEGER NOT NULL, model_type VARCHAR(255) NOT NULL, model_id INTEGER NOT NULL, PRIMARY KEY (role_id, model_id, model_type));"

# Model has permissions table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS model_has_permissions (permission_id INTEGER NOT NULL, model_type VARCHAR(255) NOT NULL, model_id INTEGER NOT NULL, PRIMARY KEY (permission_id, model_id, model_type));"

# Role has permissions table
RUN sqlite3 database/database.sqlite "CREATE TABLE IF NOT EXISTS role_has_permissions (permission_id INTEGER NOT NULL, role_id INTEGER NOT NULL, PRIMARY KEY (permission_id, role_id));"

# CREATE DEFAULT ROLES
RUN sqlite3 database/database.sqlite "INSERT OR IGNORE INTO roles (name, guard_name, created_at, updated_at) VALUES ('admin', 'web', datetime('now'), datetime('now'));"
RUN sqlite3 database/database.sqlite "INSERT OR IGNORE INTO roles (name, guard_name, created_at, updated_at) VALUES ('alumni', 'web', datetime('now'), datetime('now'));"

# CREATE DEMO USER (Password: admin123)
RUN sqlite3 database/database.sqlite "INSERT OR IGNORE INTO users (name, email, password, created_at, updated_at) VALUES ('Admin User', 'admin@appiahkubi.edu.gh', '\$2y\$12\$QjSH496pcT5CEbzHR6/rLuMSs93HpfZq6xE2.8Fn.9qNL7oXlJx.m', datetime('now'), datetime('now'));"

# ASSIGN ADMIN ROLE TO DEMO USER
RUN sqlite3 database/database.sqlite "INSERT OR IGNORE INTO model_has_roles (role_id, model_type, model_id) VALUES (1, 'App\\\\Models\\\\User', 1);"

# FIX FOR SESSION ISSUE:
RUN mkdir -p storage/framework/sessions
RUN chmod -R 775 storage/framework/sessions
RUN chown -R www-data:www-data storage/framework/sessions

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
