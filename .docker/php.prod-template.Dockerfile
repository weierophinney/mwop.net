## This is a template to inject in the production dockerfile definition

COPY .docker/etc/zendphp/conf.d/mwop.ini /entrypoint.d/conf.d/zzz-mwop.ini
COPY .docker/s6/cont-init.d/00-build.sh .docker/s6/cont-init.d/01-std-stream-permissions.sh .docker/s6/cont-init.d/99-start-message.sh /entrypoint.d/
COPY .docker/usr/local/bin /usr/local/bin/
COPY composer.* /var/www/
COPY config /var/www/config
COPY data /var/www/data
COPY public /var/www/public
COPY src /var/www/src
COPY templates /var/www/templates
RUN set -e; \
    mwopnet-build; \
    rm /usr/local/bin/mwopnet-build
