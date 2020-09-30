#!/bin/bash

# Build the environment file
/usr/bin/php /var/www/my-site/scripts/create-env-file.php

# Run migrations
/usr/bin/php /var/www/my-site/scripts/migrate.php


# Now start the webserver
#service apache2 restart
service nginx start
service php7.4-fpm start


# Start the supervisor service in the foreground
/usr/bin/supervisord
