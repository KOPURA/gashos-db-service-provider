FROM php:7.3.0-apache
# Setup SSL
RUN mkdir -p /etc/apache2/ssl
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/apache2/ssl/server.key -out /etc/apache2/ssl/server.pem -subj "/C=AT/ST=Sofia/L=Sofia/O=Security/OU=Development/CN=localhost"
RUN a2enmod ssl
COPY default-ssl.conf /etc/apache2/sites-enabled

# Install mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install AWS SDK
RUN apt-get update
RUN apt-get install unzip # So that we can unzip the AWS SDK kit

RUN curl "https://docs.aws.amazon.com/aws-sdk-php/v3/download/aws.zip" > /var/tmp/aws.zip && \
    mkdir /usr/local/lib/php/AWS                                                          && \
    unzip /var/tmp/aws.zip -d /usr/local/lib/php/AWS                                      && \
    rm -f /var/tmp/aws.zip

VOLUME ['/var/www/html']