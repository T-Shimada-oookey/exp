FROM php:8.3-fpm

ENV TZ Asia/Tokyo

# 必要なパッケージをインストール
RUN apt-get update && \
    apt-get install -y git unzip libzip-dev libicu-dev libonig-dev curl && \
    docker-php-ext-install intl pdo_mysql zip bcmath

# Node.js 20 をインストール
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    npm install -g npm@latest

EXPOSE 5173

# PHP 設定ファイルのコピー
COPY ./docker/app/php.ini /usr/local/etc/php/php.ini

# Composer のインストール
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app