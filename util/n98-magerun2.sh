#!/usr/bin/env bash

COMMAND="cd /var/www/magento && n98-magerun2.phar $@";
#echo "$COMMAND";
docker-compose exec cli bash -c "$COMMAND"
