# Dockerfile
FROM php:7.4-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

EXPOSE 80

#COPY conf/proxy /etc/apt/apt.conf.d/proxy
WORKDIR /var/www

RUN apt-get update -qy && \
    apt-get install -y \
    git \
    nano \
    vim \
    libicu-dev \
    cifs-utils \
    zlib1g-dev \
    libzip-dev \
    libonig-dev \
    libpng-dev\
    libmagickwand-dev --no-install-recommends \
    unzip \
    #    varnish \
    openssh-client \
    sshpass \
    zip 
#&& \
#apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
# PHP Extensions
RUN docker-php-ext-install -j$(nproc) opcache pdo pdo_mysql
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install exif && docker-php-ext-enable exif
RUN docker-php-ext-configure mbstring --enable-mbstring && docker-php-ext-install mbstring
RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN docker-php-ext-install zip

RUN printf "\n" | pecl install imagick
RUN docker-php-ext-enable imagick

RUN docker-php-ext-install gd

# Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# php ini
COPY php.ini /usr/local/etc/php/conf.d/app.ini

# apache
# COPY errors /errors
RUN a2enmod rewrite remoteip headers expires

# apache configuration with varnish
COPY ports.conf /etc/apache2/ports.conf
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
# COPY apache.conf /etc/apache2/conf-available/z-app.conf
#COPY ../conf/varnish.vcl /etc/varnish/default.vcl
# RUN a2enconf z-app

# deploy entrypoint
COPY docker-php-entrypoint /usr/local/bin/
RUN chmod 775 /usr/local/bin/docker-php-entrypoint

# main source code
#COPY --chown=www-data:www-data --chmod=755 www /var/www
#RUN chmod -R 755 /var/www
#RUN chown -R www-data:www-data /var/www

COPY ./ www/

#RUN mkdir /var/www/vendor
#USER www-data

# composer with no scripts
# RUN composer install --no-scripts --no-interaction --verbose

#RUN chmod -R 755 /var/www/vendor
#RUN chown -R www-data:www-data /var/www/vendor

#RUN chmod -R 755 /var/www/var
#RUN chown -R www-data:www-data /var/www/var
