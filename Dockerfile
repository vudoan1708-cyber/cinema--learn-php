FROM --platform=linux/amd64 ubuntu:20.04
ENV DEBIAN_FRONTEND=noninteractive
RUN apt update -y
RUN apt upgrade -y
RUN apt install -y software-properties-common
RUN apt-add-repository ppa:ondrej/php

RUN apt install -y wget
RUN apt install -y unzip
RUN apt install -y apache2
RUN apt install -y php7.2
RUN apt install -y php7.2-mysql
RUN apt install -y php7.2-pdo-mysql
RUN apt install -y libapache2-mod-php7.2
RUN apt install -y php7.2-curl
RUN apt install -y php7.2-dom
RUN apt install -y php7.2-gd
RUN apt install -y php7.2-imagick
RUN apt install -y php7.2-json
RUN apt install -y php7.2-common
RUN apt install -y php7.2-mbstring
RUN apt install -y php7.2-xml
RUN apt install -y php7.2-bcmath
RUN apt install -y php7.2-apcu
RUN apt install -y php7.2-xdebug

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
RUN apt-get install -y composer
COPY ./composer.json /var/www/composer.json

# Copy virtual host into container
COPY config/000-default.conf /etc/apache2/sites-available/
CMD ["apachectl","-D","FOREGROUND"]
RUN a2enmod rewrite
RUN a2enmod headers
# RUN a2dissite 000-default
# RUN a2ensite voly-vhost
# RUN unzip usrlocal.zip -d /usr/local/
EXPOSE 80
EXPOSE 443
