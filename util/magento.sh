#!/usr/bin/env bash

command_string="$@"
docker-compose exec cli bash -c -- "cd /var/www/magento && magento-command $command_string"