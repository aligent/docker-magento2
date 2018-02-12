#!/usr/bin/env bash

docker-compose exec cli sudo bash -c "cd /var/www/magento && composer $@"