FROM ubuntu:22.04

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Jakarta

# Install dependencies
RUN apt-get update && apt-get install -y \
    software-properties-common \
    build-essential \
    apache2 \
    apache2-utils \
    curl \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libsqlite3-dev \
    libcurl4-openssl-dev \
    libfreetype-dev \
    libjpeg-dev \
    libwebp-dev \
    libxpm-dev \
    libicu-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP 8.4 from Ondrej PPA
RUN apt-get update && apt-get install -y \
    language-pack-en-base \
    && LC_ALL=en_US.UTF-8 add-apt-repository -y ppa:ondrej/php \
    && apt-get update \
    && apt-get install -y \
    php8.4 \
    php8.4-cli \
    php8.4-common \
    libapache2-mod-php8.4 \
    php8.4-mysql \
    php8.4-pgsql \
    php8.4-sqlite3 \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-curl \
    php8.4-zip \
    php8.4-gd \
    php8.4-intl \
    php8.4-bcmath \
    php8.4-exif \
    && apt-get clean

# Disable conflicting MPM modules
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-req=php --ignore-platform-req=ext-gd

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure Apache
RUN echo 'ServerName localhost' >> /etc/apache2/apache2.conf

EXPOSE 80

# Start Apache
CMD ["apache2ctl", "-D", "FOREGROUND"]
