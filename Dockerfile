FROM debian:jessie

# Install Apache PHP mod and its dependencies (including Apache and PHP!)
ENV DEBIAN_FRONTEND noninteractive
RUN    apt-get update \
    && apt-get -yq install \
        curl \
        libapache2-mod-php5 \
        php5-intl \
        php5-curl \
        php5-mysql \
    && rm -rf /var/lib/apt/lists/*


# Configure Apache
RUN rm -rf /var/www/* \
    && a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf
ADD Docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Add main start script for when image launches
ADD ./docker/run.sh /run.sh
RUN chmod 0755 /run.sh

# Cleanup
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Main attributes for running the container
WORKDIR /app
EXPOSE 80
CMD ["/run.sh"]

