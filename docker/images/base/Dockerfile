FROM ubuntu

ARG SYSTEM_TZ
ARG GITHUB_TOKEN

RUN ln -snf /usr/share/zoneinfo/$SYSTEM_TZ /etc/localtime && echo $SYSTEM_TZ > /etc/timezone

RUN set -xe \
    && apt-get update \
    && apt -y install software-properties-common \
    && add-apt-repository ppa:ondrej/php \
    && apt-get update \
    && apt-get -y install \
        git \
        php7.2 \
        php7.2-dev \
        php7.2-bcmath \
        php7.2-common \
        php7.2-curl \
        php7.2-dom \
        php7.2-gd \
        php7.2-imagick \
        php7.2-mbstring \
        php7.2-mysql \
        php7.2-mysqli \
        php7.2-mysqlnd \
        php7.2-pdo \
        php7.2-tidy \
        php7.2-xml \
        php7.2-xdebug \
        php7.2-zip \
        php7.2-intl \
        libapache2-mod-php7.2 \
        php-pear \
    && apt-get -y upgrade

RUN set -xe \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer && chmod +x /usr/local/bin/composer

COPY ./config /root/.config/composer
