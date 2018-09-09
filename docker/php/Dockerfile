FROM php:7.2-fpm

RUN apt-get update
RUN apt-get install -y \
        supervisor \
        nginx \
        libcurl4-gnutls-dev \
        libsqlite3-0 \
        curl

RUN docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo \
    curl \
    json

ADD ./docker/php/php.ini /usr/local/etc/php/php.ini
ADD ./docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD ./docker/php/nginx.conf /etc/nginx/nginx.conf
ADD ./src /opt/src/
ADD ./bin /opt/bin/

EXPOSE 80
WORKDIR /opt
CMD ["/usr/bin/supervisord"]