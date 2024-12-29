# Use the official PHP 8.2 FPM image based on Alpine Linux
FROM php:8.2-fpm-alpine

# Update APK package index and install necessary packages: git, curl, zip, unzip
RUN apk update && apk add \
    git \
    curl \
    zip \
    unzip

# Install PHP extensions: mysqli, pdo, and pdo_mysql; install Node.js and npm
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && apk --no-cache add nodejs npm

# Install PHP extension dependencies
RUN apk --no-cache add libintl icu-dev icu-libs

# Install and enable the intl extension
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

# Copy the init.sh script to /usr/local/bin and make it executable
COPY init.sh /usr/local/bin/init.sh  
RUN chmod +x /usr/local/bin/init.sh  

# Download and install Composer to /usr/local/bin with the filename 'composer'
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
