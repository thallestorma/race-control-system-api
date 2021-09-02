# Set Docker image
FROM php:5.6.30-apache

# Install node
RUN curl -sL https://deb.nodesource.com/setup_8.x | bash

# Install dependencies
RUN apt-get update && apt-get -y --force-yes install \
        vim \
        git \
        unixodbc-dev \
        libcurl4-openssl-dev \
        libxml2-dev \
        libssh2-1-dev \
        libzip-dev \
        openssl \
        unzip \
	nodejs

# Create directories and files dependencies
RUN mkdir /var/www/app

# Install PHP ODBC
RUN docker-php-ext-install odbc; \
  cd /usr/src/php/ext/odbc; \
  phpize; \
  sed -ri 's@^ *test +"\$PHP_.*" *= *"no" *&& *PHP_.*=yes *$@#&@g' configure; \
  ./configure --with-unixODBC=shared,/usr; \
  docker-php-ext-configure pdo_odbc --with-pdo-odbc=unixODBC,/usr; \
  docker-php-ext-install pdo pdo_mysql mysqli zip pdo_odbc odbc

# Install and enable PHP modules
RUN docker-php-ext-install mbstring \
        xml \
        json \
        tokenizer

RUN a2enmod dav_fs && a2enmod rewrite && a2enmod headers

# Copy config files
COPY php.ini /usr/local/etc/php/
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY apache2.conf /etc/apache2/apache2.conf

# Set permission in /var/www
RUN chown -R www-data:www-data /var/www \
        /tmp \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/apache2.conf

# Instala composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Set WORKDIR
WORKDIR /var/www/app

CMD ["docker-php-entrypoint", "apache2-foreground"]