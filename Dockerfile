FROM php:8.3-fpm-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    gettext-base \
    git \
    gnupg \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libpng-dev \
    libsqlite3-dev \
    libxml2-dev \
    libzip-dev \
    nginx \
    sqlite3 \
    unzip \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd mbstring pdo pdo_mysql pdo_sqlite zip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get update && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --optimize-autoloader

# Install libSQL PHP extension using the official installer from dependencies
RUN ./vendor/bin/turso-php-installer install -n --php-version=8.3 --silent

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

RUN npm run build \
    && php artisan package:discover --ansi \
    && php artisan view:cache

COPY docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template
COPY docker/start-container.sh /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8080
ENV PORT=8080

CMD ["/usr/local/bin/start-container"]
