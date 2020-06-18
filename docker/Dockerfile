#Start with ubuntu 20.04
FROM ubuntu:20.04
MAINTAINER James Howard <jem.howard@gmail.com>

# Setup environment
ENV DEBIAN_FRONTEND noninteractive

# Update sources
RUN apt-get update -y

# install php
RUN apt-get install -y php7.4

# install zip
RUN apt-get install -y zip unzip

# install git
RUN apt-get install -y git

# install composer + prerequisites
RUN apt-get install -y php-xml
RUN apt-get install -y php-mbstring
RUN apt-get install -y curl
RUN curl -s https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin

# install app code from public repo
WORKDIR /etc
RUN git clone https://github.com/Jem72/veri_coding_test
WORKDIR /etc/veri_coding_test
RUN /usr/local/bin/composer.phar update

# execute unit tests
WORKDIR /etc/veri_coding_test/test
RUN ../vendor/bin/phpunit .

WORKDIR /etc/veri_coding_test
RUN php src/index.php