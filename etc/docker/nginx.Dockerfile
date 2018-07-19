# DOCKER-VERSION        1.3.2

FROM nginx:1.13-alpine

RUN mkdir -p /var/www/mwop.net

# nginx config
COPY etc/bin/nginx-entrypoint /usr/local/bin/
COPY etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf

# Project files
ADD public /var/www/mwop.net/public

EXPOSE 8080
CMD ["nginx-entrypoint"]
