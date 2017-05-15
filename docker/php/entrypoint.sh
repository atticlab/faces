#!/bin/bash

mkdir /logs/php
touch /logs/php/php_errors.log
chown -R www-data:www-data /logs/php

# Setup env variables to docker
printenv | perl -pe 's/^(.+?\=)(.*)$/\1"\2"/g' | cat - /crontab_tmp > /crontab
crontab -u www-data /crontab
cron

# Install packages
composer --working-dir=/src/php install
composer global require "phpunit/phpunit=4.5.*"

chown www-data:www-data /bad_photo

# Start daemon
php-fpm