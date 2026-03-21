FROM php:8.2-fpm

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 🔥 Copia só composer primeiro (usa cache do Docker)
COPY composer.json composer.lock ./

RUN composer install --no-dev --prefer-dist --no-scripts --no-progress

# Agora copia o resto
COPY . .

RUN composer dump-autoload --optimize

# Permissões
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]