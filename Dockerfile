FROM ubuntu:18.04
MAINTAINER Tobias Oitzinger <to@dive-me-in.at>

# Update packages and install apache + php7.0 + pecl
ARG DEBIAN_FRONTEND=noninteractive

# Set ENV
ENV TRAVIS_TEST=1;

# Install Required Packages
RUN cp /etc/apt/sources.list /etc/apt/sources.list~ && \
    sed -Ei 's/^# deb-src /deb-src /' /etc/apt/sources.list && \
    apt-get update && \
    apt-get -qq upgrade && \
    apt-get -qq install --no-install-recommends \
    apt-utils \
    software-properties-common \
    ca-certificates \
    curl \
    unzip \
    wget \
    language-pack-en \
    gcc \
    libcurl4-openssl-dev \
    libde265-dev \
    webp \
    libwebp-dev \
    libmagic-dev \
    redis-server \
    git \
    imagemagick && \
    apt-get -qq build-dep imagemagick && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get -qq install --no-install-recommends \
    php7.3 \
    php7.3-dev \
    php7.3-common \
    php7.3-cli \
    php7.3-mbstring \
    php7.3-gd \
    php7.3-mysql \
    php7.3-curl \
    php7.3-xml \
    php7.3-sqlite3 \
    php7.3-opcache \
    php7.3-pgsql \
    php7.3-mysql \
    php-xdebug \
    php-redis \
    php-pear \
    php-yaml \
    php-raphf \
    php-raphf-dev \
    php-propro \
    php-propro-dev \
    php-imagick \
    # pecl_http not yet, as tests will fail
    #php-http \
    #php-pecl-http \
    php-redis && \
    curl -s https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    mkdir -p /tmp/koseven && \
    echo "requirepass password" >> /etc/redis/redis.conf && \
    sed -i "s/bind .*/bind 127.0.0.1/g" /etc/redis/redis.conf
