FROM php:7.1-alpine

RUN apk --no-cache add curl nodejs make autoconf libpng libpng-dev mysql-client &&\
    docker-php-ext-configure gd \
    --with-png-dir=/usr/include/ && \
    NPROC=$(getconf _NPROCESSORS_ONLN || 1) && \
    docker-php-ext-install -j${NPROC} gd pdo_mysql opcache zip bcmath && \
    apk del --no-cache libpng-dev && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer


RUN mkdir -p /var/www/examreg

VOLUME /var/www/examreg

ADD . /var/www/examreg

RUN set -x && \
    addgroup -g 1000 -S examreguser && \
    adduser -u 1000 -D -S examreguser -G examreguser
RUN chown -R examreguser:examreguser /var/www/examreg

USER examreguser

WORKDIR /var/www/examreg

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/examreg/public"]
