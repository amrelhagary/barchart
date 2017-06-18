#!/bin/bash

# mkdir -p app/cache app/logs
# touch app/logs/prod.log
# chgrp -R www-data .
# chmod -R g+w app/cache app/logs

mkdir ../db_data
source /etc/apache2/envvars

tail -F /var/log/apache2/* /app/barchart/storage/logs/lumen.log &
exec apache2 -D FOREGROUND
