FROM php:8.3-cli-bookworm

# Prevent prompts during package installations
ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev \
    curl \
    gnupg \
    chromium \
    chromium-driver \
    libnss3 \
    libatk-bridge2.0-0 \
    libx11-xcb1 \
    libxcb-dri3-0 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxi6 \
    libxtst6 \
    libxrandr2 \
    libasound2 \
    libpangocairo-1.0-0 \
    libatk1.0-0 \
    libcups2 \
    libdrm2 \
    libxkbcommon0 \
    libxshmfence1 \
    libgbm1 \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install zip pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Node.js & NPM
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Set composer environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Copy the library codebase to the container
COPY . /app

# Install PHP and Node dependencies in the Laravel test application
WORKDIR /app/tests/laravel
RUN rm -f composer.lock && composer install --no-interaction --prefer-dist --no-scripts
RUN npm install && npm run build

# Make sure our test script is executable
RUN chmod +x run-tests.sh

# Set the default working directory
WORKDIR /app

# Define standard entrypoint CMD to run Dusk tests
CMD ["/app/tests/laravel/run-tests.sh"]
