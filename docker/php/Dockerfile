FROM php:8.1.1-apache

ARG UID

# Create user with same permissions as host and some useful stuff
RUN adduser -u ${UID} --disabled-password --gecos "" appuser
RUN mkdir /home/appuser/.ssh
RUN chown -R appuser:appuser /home/appuser/
RUN echo "StrictHostKeyChecking no" >> /home/appuser/.ssh/config
RUN echo "export COLUMNS=300" >> /home/appuser/.bashrc
RUN echo "alias sf=/var/www/html/bin/console" >> /home/appuser/.bashrc

# Install packages and PHP extensions
RUN apt update \
    # common libraries and extensions
    && apt install -y git acl openssl openssh-client wget zip \
    && apt install -y libpng-dev zlib1g-dev libzip-dev libxml2-dev libicu-dev \
    && docker-php-ext-install intl pdo zip \
    # for MySQL
    && docker-php-ext-install pdo_mysql \
    # for Postgres
    && apt install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    # for RabbitMQ and XDEBUG
    && docker-php-ext-install bcmath sockets \
    && apt install -y librabbitmq-dev vim libssh-dev \
    && pecl install xdebug amqp-1.11.0RC1 \
    # XDEBUG and APCu
    && pecl install xdebug apcu \
    # enable Docker extensions
    && docker-php-ext-enable --ini-name 05-opcache.ini opcache xdebug amqp apcu

# Install and update composer
RUN curl https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer
RUN composer self-update

## Install Symfony binary
USER appuser
RUN wget https://get.symfony.com/cli/installer -O - | bash
USER root
#RUN #mv /home/appuser/.symfony/bin/symfony /usr/local/bin/symfony
# for symfony 5
RUN mv /home/appuser/.symfony5/bin/symfony /usr/local/bin/symfony

RUN mkdir -p /var/www/html

# Install PHP-CS-FIXER
RUN wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O php-cs-fixer
RUN chmod a+x php-cs-fixer
RUN mv php-cs-fixer /usr/local/bin/php-cs-fixer

# Config XDEBUG
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Update Apache config
COPY ./default.conf /etc/apache2/sites-available/default.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && a2dissite 000-default \
    && a2ensite default \
    && service apache2 restart

# Modify upload file size
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html
