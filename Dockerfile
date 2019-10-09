FROM php:7-fpm

RUN apt-get update; \
	apt-get install openssl libssl-dev libcurl4-openssl-dev -y; \
    pecl install xdebug \
        && docker-php-ext-enable xdebug; \
	pecl install mongodb; \
	echo "extension=mongodb.so" >> /usr/local/etc/php/conf.d/mongo.ini;