FROM php:8.4-apache

# Install PHP Extensions
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
        bash \
        curl \
        sudo \
        libxml2-dev \
        libicu-dev \
        wget \
        ca-certificates \
        libu2f-udev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        libxrender1 libfontconfig \
        libicu-dev \
        libgeoip-dev \
        git \
        libmagickwand-dev --no-install-recommends \
    	libmcrypt-dev \
        libxslt-dev \
    	libssh2-1-dev libssh2-1 \
    	ssh \
    	xdg-utils \
        zip libzip-dev unzip \
    	nano vim \
        exif \
        yamllint
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install zip pdo pdo_mysql opcache bcmath calendar
RUN docker-php-ext-install intl
RUN docker-php-ext-configure intl
RUN docker-php-ext-install ftp
RUN docker-php-ext-configure ftp
RUN docker-php-ext-configure calendar
RUN docker-php-ext-install xsl
RUN docker-php-ext-install sockets

# Install mysql client
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y default-mysql-client

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash \
  && apt install symfony-cli

# install yarn
RUN apt-get update && apt-get install -y gnupg2
RUN curl -s https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor > /etc/apt/trusted.gpg.d/yarn.gpg
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get update && apt-get install -y yarn

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install PECL Extensions
RUN pecl install xdebug-3.4.2 \
    && printf '; Enable xdebug extension modules\n\
zend_extension = xdebug.so\n\
xdebug.mode = debug,develop\n\
xdebug.client_host=host.docker.internal\n\
xdebug.client_port=9003\n\
xdebug.discover_client_host = 1\n\' >> $PHP_INI_DIR/conf.d/xdebug.ini

RUN pecl install apcu

RUN printf '; Disable JIT\n\
opcache.jit_buffer_size = 0\n' | sudo tee $PHP_INI_DIR/conf.d/jit.ini

# Change conf
RUN printf '; Increase PHP Memory Limit\n\
short_open_tag = off\n\
memory_limit = 128M' >> $PHP_INI_DIR/conf.d/memory_limit.ini

RUN printf '; Increase PHP Upload Limit\n\
upload_max_filesize = 256M\n\
post_max_size = 300M' >> $PHP_INI_DIR/conf.d/upload_max_size.ini

RUN printf '; Set timezone\n\
timezone = Europe/Paris' >> $PHP_INI_DIR/conf.d/timezone.ini
# Set localtime
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"

COPY cert/mycert.crt /etc/ssl/certs/mycert.crt
COPY cert/mycert.key /etc/ssl/private/mycert.key
COPY sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY sites-available/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf


# Enable SSL
RUN a2enmod rewrite && a2enmod ssl && a2enmod socache_shmcb
RUN sed -i '/SSLCertificateFile.*snakeoil\.pem/c\SSLCertificateFile \/etc\/ssl\/certs\/mycert.crt' /etc/apache2/sites-available/default-ssl.conf && \
    sed -i '/SSLCertificateKeyFile.*snakeoil\.key/cSSLCertificateKeyFile /etc/ssl/private/mycert.key\' /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl

# Custom apache2.conf
COPY apache2.conf /etc/apache2/apache2.conf

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Use www-data as default user
RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data
RUN echo '%www-data ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers
RUN mkdir -p /var/www/html/var/log
RUN mkdir -p /var/www/html/var/sessions
RUN mkdir -p /var/www/html/var/cache
RUN mkdir -p /var/www/.ssh
RUN mkdir -p /var/www/.composer
RUN sudo chown -Rf www-data:www-data /var/www
USER www-data:www-data
WORKDIR /var/www/html
RUN mkdir /var/www/.bash/

SHELL ["/bin/bash", "-l", "-c"]
