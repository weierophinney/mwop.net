# DOCKER-VERSION        1.3.2

# Build the PHP container
FROM phpswoole/swoole:4.5.6-php7.4

# System dependencies
RUN apt update \
    && apt install -y \
        libbz2-dev \
        libicu-dev \
        libtidy-dev \
        libxslt1-dev \
        libzip-dev \
    && apt clean


# PHP Extensions
RUN docker-php-ext-install -j$(nproc) bz2 \
    && docker-php-ext-install -j$(nproc) intl \
    && docker-php-ext-install -j$(nproc) opcache \
    && docker-php-ext-install -j$(nproc) pcntl \
    && docker-php-ext-install -j$(nproc) tidy \
    && docker-php-ext-install -j$(nproc) xsl \
    && docker-php-ext-install -j$(nproc) zip

# Overwrite entrypoint
COPY etc/bin/php-entrypoint /usr/local/bin/entrypoint

# Build project
WORKDIR /var/www
ENTRYPOINT ["/usr/local/bin/entrypoint"]
