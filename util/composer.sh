#!/usr/bin/env bash

docker-compose exec cli sudo -u www-data bash -c "cd /var/www/magento && composer $@"