FROM php:7.0-fpm

ENV PHPREDIS_VERSION php7

RUN apt-get update && apt-get install -y \
    git cron libpq-dev libxml2-dev htop nano \
    && docker-php-ext-install soap \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo pdo_mysql \
    # Phalcon
    && git clone --depth=1 -b 3.0.x git://github.com/phalcon/cphalcon.git /usr/local/src/cphalcon \
    && cd /usr/local/src/cphalcon/build && ./install \
    && echo "extension=phalcon.so" > /usr/local/etc/php/conf.d/25-phalcon.ini \

    # Composer
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add crontab file in the cron directory
ADD config/crontab /crontab_tmp

# Add php.ini
ADD config/php.ini $PHP_INI_DIR/php.ini

ADD ./entrypoint.sh /

ENV PATH $PATH:/root/.composer/vendor/bin

CMD ["/entrypoint.sh"]