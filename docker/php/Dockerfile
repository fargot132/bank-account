FROM php:8.3-fpm AS php_base

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer:latest --link /usr/bin/install-php-extensions /usr/local/bin/

ARG LOCAL_UID
ARG LOCAL_GID

RUN if [ ${LOCAL_UID:-0} -ne 0 ] && [ ${LOCAL_GID:-0} -ne 0 ]; then \
    usermod -u ${LOCAL_UID} www-data &&\
    groupmod -g ${LOCAL_GID} www-data \
;fi

RUN install-php-extensions zip pdo_mysql xdebug

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY ./docker/php/conf.d /usr/local/etc/php/conf.d
COPY ./docker/php/php-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/php-entrypoint.sh
RUN adduser www-data sudo && echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

ENTRYPOINT ["php-entrypoint.sh"]

FROM php_base AS php

