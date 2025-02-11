# From https://github.com/docker-library/php/blob/master/8.0/buster/apache/Dockerfile
FROM php:8.0-apache-bullseye

EXPOSE 80
EXPOSE 8080
EXPOSE 8443

RUN mkdir -p /mnt/docker-aio-config/;

VOLUME /mnt/docker-aio-config/

RUN mkdir -p /var/www/docker-aio;

WORKDIR /var/www/docker-aio

RUN apt-get update; \
    apt-get install -y --no-install-recommends \
        git \
        supervisor \
        openssl \
        sudo \
    ; \
    rm -rf /var/lib/apt/lists/*

RUN curl "https://caddyserver.com/api/download?os=linux&arch=amd64" -o "/usr/bin/caddy" \
    && chmod +x /usr/bin/caddy \
    && /usr/bin/caddy version

RUN cd /var/www/docker-aio; \
    git clone https://github.com/nextcloud/all-in-one.git .; \
    chmod 770 -R ./; \
    chown www-data:www-data -R ./; \
    rm -r ./php/data; \
    rm -r ./php/session

RUN mkdir -p /etc/apache2/certs && \
    cd /etc/apache2/certs && \
    openssl req -new -newkey rsa:4096 -days 3650 -nodes -x509 -subj "/C=DE/ST=BE/L=Local/O=Dev/CN=nextcloud.local" -keyout ./ssl.key -out ./ssl.crt;

COPY mastercontainer.conf /etc/apache2/sites-available/

RUN a2enmod rewrite \
    headers \
    env \
    mime \
    dir \
    authz_core \
    proxy \
    proxy_http \
    ssl

RUN rm /etc/apache2/ports.conf; \
    sed -s -i -e "s/Include ports.conf//" /etc/apache2/apache2.conf; \
    sed -i "/^Listen /d" /etc/apache2/apache2.conf

RUN a2dissite 000-default && \
    a2dissite default-ssl && \
    a2ensite mastercontainer.conf && \
    service apache2 restart

RUN mkdir /var/log/supervisord; \
    mkdir /var/run/supervisord;

RUN mkdir -p /usr/src/php/ext/apcu && \
    curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1 && \
    docker-php-ext-install apcu

COPY Caddyfile /
COPY start.sh /usr/bin/
COPY cron.sh /
COPY supervisord.conf /
RUN chmod +x /usr/bin/start.sh; \
    chmod +x /cron.sh

USER root

ENTRYPOINT ["start.sh"]
CMD ["/usr/bin/supervisord", "-c", "/supervisord.conf"]
