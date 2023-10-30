FROM php:8.2-fpm-bullseye
LABEL MAINTAINER="Walker de Alencar<walkeralencar@gmail.com>"

WORKDIR /var/www

ENV WORKDIR=/var/www
ENV STORAGE_DIR=${WORKDIR}/storage

RUN apt-get update -y && \
    apt-get install -y --no-install-recommends apt-utils supervisor && \
    apt-get install -y zlib1g-dev libzip-dev unzip libpng-dev libpq-dev libxml2-dev \
                       libfreetype6-dev libjpeg62-turbo-dev libonig-dev

RUN docker-php-ext-install session xml zip iconv simplexml pcntl gd fileinfo mbstring \
                        exif bcmath mysqli pdo pdo_mysql pdo_pgsql pgsql

# Copying config files
#COPY ./.docker/backend/php.ini /etc/php/8.1/cli/conf.d/php.ini
# COPY ./.docker/supervisord.conf /etc/supervisor/supervisord.conf

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Wait
ENV WAIT_VERSION 2.9.0
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/$WAIT_VERSION/wait /wait
RUN chmod +x /wait

EXPOSE 80

#CMD ["php-fpm"]
