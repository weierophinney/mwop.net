# DOCKER-VERSION        1.3

FROM cr.zend.com/zendphp/8.1:ubuntu-20.04-cli

## Prepare image
ARG SWOOLE_VERSION=4.11.1
ARG TIMEZONE=UTC
ENV TZ=$TIMEZONE
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN set -e; \
    apt-get update; \
    apt-get install -y php8.1-zend-dev libcurl4-openssl-dev; \
    mkdir /workdir; \
    cd /workdir; \
    curl -L -o openswoole-${SWOOLE_VERSION}.tgz https://pecl.php.net/get/openswoole-${SWOOLE_VERSION}.tgz; \
    tar xzf openswoole-${SWOOLE_VERSION}.tgz; \
    cd openswoole-${SWOOLE_VERSION}; \
    phpize8.1-zend; \
    ./configure \
        --with-php-config=/usr/bin/php-config8.1-zend \
        --enable-http2 \
        --enable-openssl \
        --enable-sockets \
        --enable-openswoole \
        --enable-swoole-curl \
        --enable-swoole-json; \
    make; \
    make install; \
    apt-get remove -y php8.1-zend-dev libcurl4-openssl-dev; \
    apt-get autoremove -y; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*
