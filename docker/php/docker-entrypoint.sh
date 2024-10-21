#!/bin/sh
set -e

sh docker/php/setup-composer.sh

composer install

exec docker-php-entrypoint "php-fpm"
