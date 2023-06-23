FROM ubuntu:22.04

# Arguments defined in docker-compose.yml
#ARG user
#ARG uid

ARG WWWGROUP

# Set working directory
WORKDIR /var/www

ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN apt-get update \
        && apt-get install -y gnupg gosu curl ca-certificates zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python2 \
        && apt-get install -y php8.1-cli php8.1-dev \
       php8.1-pgsql php8.1-sqlite3 php8.1-gd php-pdo-mysql\
       php8.1-curl \
       php8.1-imap php8.1-mysql php8.1-mbstring \
       php8.1-xml php8.1-zip php8.1-bcmath php8.1-soap \
       php8.1-intl php8.1-readline \
       php8.1-ldap \
       php8.1-msgpack php8.1-igbinary php8.1-redis \
       php8.1-memcached php8.1-pcov php8.1-xdebug \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

COPY start-container.sh /usr/local/bin/start-container.sh
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY . /var/www
# Install PHP extensions
#RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
#RUN useradd -G www-data,root -u $uid -d /home/$user $user
#RUN mkdir -p /home/$user/.composer && \
#RUN chown -R $user:$user /home/$user && \
#RUN chmod +x /usr/local/bin/start-container.sh
RUN chown -R www-data:www-data /var/www
RUN chmod -R 775 /var/www
RUN chmod -R 775 /var/www/vendor
RUN chmod -R 777 /var/www/storage

#CMD ["sh","/usr/local/bin/start-container.sh"]

 CMD ["supervisord","-c","/etc/supervisor/conf.d/supervisord.conf"]


EXPOSE 80
