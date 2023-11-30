## This is a template to inject in the production dockerfile definition

COPY .docker/etc/nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY .docker/etc/nginx/conf.d/jq.conf /etc/nginx/conf.d/jq.conf
COPY .docker/etc/nginx/mime.types /etc/nginx/mime.types
COPY .docker/etc/nginx/nginx.conf /etc/nginx/nginx.conf
COPY public /var/www/public
