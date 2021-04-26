# Due to layout of this project, the dockerfile will be moved up two directories and run during
# the build. Thus when performing any ADD commands, remember that this is "where you are"

FROM ubuntu:20.04

ENV DEBIAN_FRONTEND=noninteract

RUN apt-get update \
    && apt-get dist-upgrade -y \
    && apt-get install -y software-properties-common apt-transport-https composer vim nginx php-fpm curl supervisor cron \
       php7.4-cli php7.4-xml php7.4-zip php7.4-mbstring php7.4-bcmath php7.4-curl php7.4-mysql

# expose port 80 and 443 (ssl) for the web requests
EXPOSE 80
EXPOSE 443

###### Update the php INI settings #########
# increase security by configuring php-fpm to only execute exact matches for files,
# rather than executing the closest matching file.
# Comment out the memory limit in fpm that we can set it dynamically on the application layer.
# also set display errors to on, will disable in PHP if in production environment
RUN sed -i "s|;cgi.fix_pathinfo=.*|cgi.fix_pathinfo=1|" /etc/php/7.4/fpm/php.ini \
    && sed -i 's/memory_limit = .*/;memory_limit = -1/' /etc/php/7.4/fpm/php.ini \
    && sed -i 's/memory_limit = .*/memory_limit = -1/' /etc/php/7.4/cli/php.ini \
    && sed -i 's;display_errors = .*;display_errors = On;' /etc/php/7.4/fpm/php.ini \
    && sed -i 's;display_errors = .*;display_errors = On;' /etc/php/7.4/cli/php.ini

####### END of updating php INI ########

# Replace the nginx base configuration file (set default settings etc).
ADD docker/nginx.conf /etc/nginx/nginx.conf

# Update our nginx sites available with the config we created
ADD docker/nginx-site-config.conf /etc/nginx/sites-available/default

# overwerite the php.ini configution with our own.
ADD docker/php.ini /etc/php/7.4/fpm/php.ini

# Add our supervisor config to the container
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Replace the php-fpm pool config so that we listen on TCP port 9000 instead of using a local
# unix socket. Also set dynamic or static pool with number or processes.
# https://serverfault.com/questions/884468/nginx-with-php-fpm-resource-temporarily-unavailable-502-error/884477#884477
ADD docker/php-fpm-pool.conf /etc/php/7.4/fpm/pool.d/www.conf


# setup the cron service (installed earlier)
ADD docker/crons.conf /root/crons.conf
RUN crontab /root/crons.conf

# Add the site's code to the container.
# We could mount it with volume, but by having it in the container, deployment is easier.
COPY --chown=root:www-data site /var/www/my-site

# Run composer install to install packages
WORKDIR /var/www/my-site
RUN composer install && chown root:www-data -R vendor


#COPY ssl /etc/nginx/ssl


# Use the crontab file.
# The crontab file was already added when we added "project"
#RUN crontab /var/www/my-site/project/docker/crons.conf


# Set permissions
RUN chown root:www-data /var/www \
    && chmod 750 -R /var/www


# Add the startup script to the container
COPY docker/startup.sh /root/startup.sh


# Execute the containers startup script which will start many processes/services
# The startup file was already added when we added "project"
CMD ["/bin/bash", "/root/startup.sh"]
