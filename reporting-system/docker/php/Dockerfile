FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip nginx \
    libzip-dev libonig-dev \
    libcurl4-openssl-dev libssl-dev \
    librdkafka-dev default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip mbstring pcntl \
    && docker-php-ext-configure curl \
    && docker-php-ext-install curl

RUN git clone --depth 1 https://github.com/arnaud-lb/php-rdkafka.git /tmp/php-rdkafka \
    && cd /tmp/php-rdkafka \
    && phpize && ./configure \
    && make -j$(nproc) && make install \
    && docker-php-ext-enable rdkafka \
    && rm -rf /tmp/php-rdkafka

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

RUN mkdir -p /var/run/php

COPY docker/php/nginx.conf /etc/nginx/sites-enabled/default
COPY docker/php/start.sh /start.sh
RUN chmod +x /start.sh

WORKDIR /var/www/html

EXPOSE 80

CMD ["/start.sh"]