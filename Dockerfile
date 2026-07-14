FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 20
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# Install PHP dependencies first (layer cache)
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Install Node dependencies
COPY package.json package-lock.json* ./
RUN npm install

# Copy the rest of the application
COPY . .

# Finish composer autoload
RUN composer dump-autoload --optimize

# Make the entrypoint executable
RUN chmod +x /var/www/docker-entrypoint.sh

EXPOSE 8000 5173

ENTRYPOINT ["/var/www/docker-entrypoint.sh"]