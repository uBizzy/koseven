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
    gcc && \
    add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get -qq install --no-install-recommends \
    # Install english language pack
    language-pack-en \
    libcurl4-openssl-dev \
    libmagic-dev \
    libwebp-dev \
    libde265-dev \
    redis-server \
    git \
    wget \
    alien \
    webp \
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
    php-imagick \
    php-xdebug \
    php-redis \
    php-pear \
    php-yaml \
    php-raphf \
    php-raphf-dev \
    php-propro \
    php-propro-dev \
    # pecl_http not yet, as tests will fail
    #php-http \
    #php-pecl-http \
    php-redis && \
    apt-get -qq build-dep imagemagick && \
    wget https://imagemagick.org/download/linux/CentOS/x86_64/ImageMagick-7.0.8-56.x86_64.rpm && \
    wget https://imagemagick.org/download/linux/CentOS/x86_64/ImageMagick-libs-7.0.8-56.x86_64.rpm && \
    alien -d ImageMagick-7.0.8-56.x86_64.rpm && \
    alien -d ImageMagick-libs-7.0.8-56.x86_64.rpm && \
    dpkg -i imagemagick* && \
    ldconfig /usr/lib64 && \
    curl -s https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    mkdir -p /tmp/koseven && \
    echo "requirepass password" >> /etc/redis/redis.conf && \
    sed -i "s/bind .*/bind 127.0.0.1/g" /etc/redis/redis.conf
