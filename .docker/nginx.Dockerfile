# DOCKER-VERSION        1.3

# Build assets
FROM node:16.13 as assets
RUN set -e; \
    echo "Installing make (required for building assets)"; \
    apt-get update; \
    apt-get install -y make; \
    echo "Installing agentkeepalive NPM module (required for npm upgrade)"; \
    npm install -g agentkeepalive --save; \
    echo "Upgrading npm to latest version"; \
    npm install -g npm@8.x; \
    echo "Installing PostCSS"; \
    npm install -g postcss-cli; \
    echo "Creating build directory"; \
    mkdir /build

COPY assets /build/assets
COPY src /build/src
COPY templates /build/templates

WORKDIR /build/assets
RUN set -e; \
    if [ -d "node_modules" ];then \
        echo "Removing existing installed node modules"; \
        rm -rf node_modules; \
    fi; \
    echo "Installing asset dependencies"; \
    npm install; \
    echo "Building assets"; \
    make assets

# Build the nginx container
FROM nginx:1-alpine

## TEMPLATED ##

## Create document root if not already created
RUN set -e; \
    mkdir -p /var/www/public

## Install assets
COPY --from=assets /build/assets/dist /var/www/public/assets

## Expose 80
EXPOSE 80
