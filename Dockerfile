FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
    && docker-php-ext-install pdo pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# ✅ Tambahkan baris ini
RUN cp .env.example .env

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# ✅ Baru generate key sekarang
RUN php artisan key:generate

# Expose port
EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=${PORT}

