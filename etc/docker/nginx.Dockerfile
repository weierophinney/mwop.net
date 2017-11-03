# DOCKER-VERSION        1.3.2

FROM nginx:1.13-alpine

RUN mkdir -p /etc/nginx/ssl
RUN mkdir -p /var/www/mwop.net

# System dependencies
RUN apk --no-cache add openssl curl
RUN curl https://get.acme.sh | sh
RUN ln -s /root/.acme.sh/acme.sh /usr/local/bin/acme.sh

# nginx config
COPY etc/bin/nginx-entrypoint /usr/local/bin/
COPY etc/nginx/nginx.conf /etc/nginx/conf.d/default.conf

# Project files
ADD public /var/www/mwop.net/public

EXPOSE 80 443
CMD ["nginx-entrypoint"]
