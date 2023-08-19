## This is a template to inject in the production dockerfile definition

COPY .docker/etc/zendphp/conf.d/mwop.ini /entrypoint.d/conf.d/zzz-mwop.ini
COPY .docker/etc/supervisord/supervisord.conf /etc/supervisord/supervisord.conf
COPY .docker/s6/cont-init.d/00-worker-build.sh .docker/s6/cont-init.d/01-std-stream-permissions.sh .docker/s6/cont-init.d/01-worker-pidfile-permissions.sh .docker/s6/cont-init.d/99-start-message.sh /entrypoint.d/
COPY .docker/s6/services.d/supervisor /etc/services.d/supervisor
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
