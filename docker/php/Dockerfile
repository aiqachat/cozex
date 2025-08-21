FROM php:7.4-fpm
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update \
    && apt install -y libpng-dev libjpeg-dev zlib1g-dev libzip-dev imagemagick libmagick++-dev libssl-dev libcurl4-openssl-dev \
    && pecl install redis imagick \
    && docker-php-ext-install pdo_mysql gd zip opcache pcntl\
    && docker-php-ext-enable redis imagick opcache pcntl\
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy PHP configuration files
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache-custom.ini
COPY ./php.ini /usr/local/etc/php/php.ini
COPY ./php-fpm.conf /usr/local/etc/php-fpm.conf
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf