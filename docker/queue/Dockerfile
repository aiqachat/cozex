FROM php:7.4-cli
WORKDIR /var/www/html

# Install dependencies
RUN apt-get update \
    && apt install -y libpng-dev libjpeg-dev zlib1g-dev libzip-dev imagemagick libmagick++-dev libssl-dev libcurl4-openssl-dev supervisor \
    && pecl install redis \
    && docker-php-ext-install pdo_mysql gd zip opcache pcntl\
    && docker-php-ext-enable redis opcache pcntl\
    && apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Copy configuration files
COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache-custom.ini
COPY ./conf /etc/supervisor/conf.d

CMD ["/usr/bin/supervisord", "--nodaemon"]