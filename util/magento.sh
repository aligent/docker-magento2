#!/usr/bin/env bash

docker-compose exec cli bash -c "cd /var/www/magento && magento-command $@"
#echo  "cd /var/www/magento && magento-command $@"