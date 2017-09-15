# This file is used to automatically build the container used in docker-compose.yml
# For more details check https://hub.docker.com/r/werkspot/message-bus/builds/
FROM php:7.1-alpine

RUN apk add --no-cache --virtual .ext-deps autoconf g++ make && \
    pecl install xdebug && \
    pecl clear-cache
