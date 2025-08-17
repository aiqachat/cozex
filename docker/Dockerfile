FROM php:7.4-apache

# 安装系统依赖
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif gd zip

# 安装Redis扩展
RUN pecl install redis \
    && docker-php-ext-enable redis

# 安装Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 设置工作目录
WORKDIR /var/www/html

# 配置Apache
RUN a2enmod rewrite expires headers

# 删除默认配置并添加新配置
RUN rm /etc/apache2/sites-enabled/000-default.conf
COPY ./apache.conf /etc/apache2/sites-available/000-default.conf
RUN ln -s /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-enabled/000-default.conf

# 暴露端口
EXPOSE 80

# 启动命令
CMD ["apache2-foreground"]
