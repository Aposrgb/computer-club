FROM php:8.1-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
            ssh \
            git \
            nano \
            zlib1g-dev \
            libxml2-dev \
            libzip-dev \
	        libpq-dev \
            libxslt-dev \
            libyaml-dev \
            unzip \
            tig

RUN  pecl install yaml && echo "extension=yaml.so" > /usr/local/etc/php/conf.d/ext-yaml.ini && docker-php-ext-enable yaml

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install \
            zip \
            intl \
            pgsql \
            pdo pdo_pgsql

RUN echo "file_uploads = On\n" \
         "memory_limit = 1024M\n" \
         "upload_max_filesize = 128M\n" \
         "post_max_size = 128M\n" \
         "max_execution_time = 900\n" \
         > /usr/local/etc/php/conf.d/uploads.ini \
     && echo 'date.timezone = "UTC"' >> /usr/local/etc/php/conf.d/datetime.ini


RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/club/
