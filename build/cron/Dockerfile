FROM php:8.1-fpm

RUN apt update \
    && apt install -y ssh \
                     npm \
                     git \
                     zip \
                     unzip \
                     nano \
                     zlib1g-dev \
                     libxml2-dev \
                     libzip-dev \
                     libxslt-dev \
                     libyaml-dev \
                     libfreetype6-dev \
                     libjpeg62-turbo-dev \
                     gnupg2 \
                     libpng-dev \
                     cron \
                     rsyslog \
    && docker-php-ext-install intl opcache \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN apt install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql


ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.11/supercronic-linux-amd64 \
    SUPERCRONIC=supercronic-linux-amd64 \
    SUPERCRONIC_SHA1SUM=a2e2d47078a8dafc5949491e5ea7267cc721d67c

RUN curl -fsSLO "$SUPERCRONIC_URL" \
 && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
 && chmod +x "$SUPERCRONIC" \
 && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
 && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

COPY crontab /app/crontab

CMD ["supercronic", "/app/crontab"]

WORKDIR /var/www/club
