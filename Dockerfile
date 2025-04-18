FROM php:8.4

RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install mysqli

# Install git and unzip
RUN apt-get update \
    && apt-get install -y git p7zip-full unzip \
    && rm -rf /var/lib/apt/lists/* \
    && git config --global --add safe.directory /opt/traPDOor

# Install composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php composer-setup.php; \
    php -r "unlink('composer-setup.php');"; \
    mv composer.phar /usr/local/bin/composer
