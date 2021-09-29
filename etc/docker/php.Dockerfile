# DOCKER-VERSION        1.3

# Build assets
FROM node:15.12 as assets
RUN set -e; \
    echo "Installing Grunt"; \
    npm install -g grunt-cli

COPY assets /assets
WORKDIR assets
RUN set -e; \
    if [ -d "node_modules" ];then \
        echo "Removing existing installed node modules"; \
        rm -rf node_modules; \
    fi; \
    echo "Installing agentkeepalive NPM module (required for npm upgrade)"; \
    npm install -g agentkeepalive --save; \
    echo "Upgrading npm to latest version"; \
    npm install -g npm@latest; \
    echo "Installing asset dependencies"; \
    npm install --sass-binary-name=linux-x64-88; \
    echo "Building assets"; \
    grunt

# Build the PHP container
FROM phpswoole/swoole:4.7-php8.0

# System dependencies
RUN set -e; \
    apt update; \
    apt install -y \
        cron \
        curl \
        libbz2-dev \
        libicu-dev \
        libtidy-dev \
        libxslt1-dev \
        libzip-dev; \
    apt clean

# PHP Extensions
RUN set -e; \
    docker-php-ext-install -j$(nproc) bz2; \
    docker-php-ext-install -j$(nproc) intl; \
    docker-php-ext-install -j$(nproc) opcache; \
    docker-php-ext-install -j$(nproc) pcntl; \
    docker-php-ext-install -j$(nproc) tidy; \
    docker-php-ext-install -j$(nproc) xsl; \
    docker-php-ext-install -j$(nproc) zip;

# Build application
WORKDIR /var/www
RUN set -e; \
    composer self-update --no-interaction

# Install assets
COPY --from=assets /assets/build/js public/
COPY --from=assets /assets/build/css public/

ENTRYPOINT ["/usr/local/bin/entrypoint"]
