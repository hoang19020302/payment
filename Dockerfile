FROM php:8.3-cli

# Cài các thư viện cần thiết để build Swoole và chạy Laravel
RUN apt-get update && apt-get install -y \
    git unzip curl zip libzip-dev libpng-dev libxml2-dev \
    libcurl4-openssl-dev libssl-dev pkg-config \
    libpq-dev libonig-dev libbrotli-dev build-essential \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Cài các PHP extensions cần thiết cho Laravel
RUN docker-php-ext-install pdo_mysql pdo_pgsql zip sockets mbstring exif pcntl bcmath gd

# Cài PHP Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Cấu hình php.ini
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY php-custom.ini "$PHP_INI_DIR/conf.d/99-php-custom.ini"

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Build Swoole từ source với các option cần thiết
RUN set -ex && \
    git clone https://github.com/swoole/swoole-src.git /usr/src/swoole && \
    cd /usr/src/swoole && \
    phpize && \
    ./configure \
        --enable-sockets \
        --enable-openssl \
        --enable-mysqlnd \
        --enable-swoole-curl \
        --enable-brotli \
        --enable-swoole-pgsql \
        && \
    make -j"$(nproc)" && make install && \
    echo "extension=swoole.so" > /usr/local/etc/php/conf.d/swoole.ini && \
    rm -rf /usr/src/swoole

# Làm việc trong thư mục app
WORKDIR /var/www

# Copy mã nguồn Laravel (nếu cần)
COPY . .

# Tạo .env nếu chưa có
RUN [ ! -f .env ] && cp .env.example .env || true

# Cài composer
RUN composer install --no-dev --optimize-autoloader || true

RUN chmod -R 775 storage bootstrap/cache

# Cài Octane và cache cấu hình (nếu chưa chạy)
RUN php artisan octane:install --server=swoole || true
RUN php artisan config:cache || true
RUN php artisan route:cache || true

# Mở cổng chạy Laravel Octane
EXPOSE 8000

# Chạy Laravel Octane với Swoole
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8000"]
