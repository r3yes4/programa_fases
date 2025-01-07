FROM php:8.1-apache


RUN apt-get update && \
    apt-get install -y python3 python3-requests && \
    apt-get clean


RUN sed -i 's/;extension=pdo_mysql/extension=pdo_mysql/' /usr/local/etc/php/php.ini-production && \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini


RUN chown -R www-data:www-data /var/www/html