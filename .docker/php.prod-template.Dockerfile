## This is a template to inject in the production dockerfile definition

COPY .docker/etc/zendphp/conf.d/mwop.ini /etc/zendphp/conf.d/99-mwop.ini
COPY .docker/s6/cont-init.d/00-build.sh .docker/s6/cont-init.d/01-std-stream-permissions.sh .docker/s6/cont-init.d/99-start-message.sh /etc/cont-init.d/
COPY .docker/usr/local/bin/mwopnet-build /usr/local/bin/mwopnet-build
COPY composer.* /var/www/
COPY config /var/www/config
COPY data /var/www/data
COPY public /var/www/public
COPY src /var/www/src
COPY templates /var/www/templates
RUN set -e; \
    mwopnet-build; \
    rm /usr/local/bin/mwopnet-build
