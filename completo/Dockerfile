# Usar la imagen oficial de PHP 8.1 con Apache
FROM php:8.1-apache

# Instalar dependencias necesarias
RUN apt-get update && \
    apt-get install -y \
      python3 \
      python3-pip \
      python3-requests \
      libcurl4-openssl-dev \
      libssl-dev \
      pkg-config \
      libpq-dev \
      unzip && \
    # Instalar el conector de MySQL para Python
    pip install mysql-connector-python --break-system-packages && \
    pip install pymongo --break-system-packages && \
    pip install pycryptodome --break-system-packages && \
    # Instalar dependencias para PHP
    docker-php-source extract && \
    apt-get install -y libonig-dev && \
    # Instalar extensiones de PHP necesarias
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-source delete && \
    
    # Limpiar los paquetes temporales para reducir el tamaño de la imagen
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Instalar y habilitar la extensión de MongoDB en PHP
RUN pecl install mongodb && \
    docker-php-ext-enable mongodb

# Configurar PHP para habilitar las extensiones necesarias
RUN sed -i 's/;extension=pdo_mysql/extension=pdo_mysql/' /usr/local/etc/php/php.ini-production && \
    echo "extension=mongodb" >> /usr/local/etc/php/php.ini-production && \
    echo "upload_max_filesize = 2048M" >> /usr/local/etc/php/php.ini-production && \
    echo "post_max_size = 2100M" >> /usr/local/etc/php/php.ini-production && \
    echo "memory_limit = 2048M" >> /usr/local/etc/php/php.ini-production && \
    cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN 
# Asignar permisos adecuados a la carpeta de Apache
RUN chown -R www-data:www-data /var/www/html



