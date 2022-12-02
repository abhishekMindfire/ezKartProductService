FROM php:8.1.2-apache

USER root

WORKDIR /var/www/html/laravelMS/ezKartProductService

RUN apt-get update && apt-get install -y \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        zip \
        curl \
        unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-source delete


    COPY .docker/vhost2.conf /etc/apache2/sites-available/000-default.conf

    RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

    RUN chown -R www-data:www-data /var/www/html/laravelMS/ezKartProductService \
        && a2enmod rewrite && a2enmod proxy && a2enmod proxy_http

    # Clear cache
    RUN apt-get clean && rm -rf /var/lib/apt/lists/*

    # Install PHP extensions
    RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

    COPY  .docker/docker-entrypoint.sh /entrypoint.sh
    RUN chmod +x /entrypoint.sh
    ENTRYPOINT ["/entrypoint.sh"]

#    CMD ["php-fpm"]




