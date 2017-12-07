#!/usr/bin/env bash

docker-compose exec cli bash -c "cd /var/www/magento && n98-magerun2.phar \"$@\""