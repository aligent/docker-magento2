#!/usr/bin/env bash


if [[ $# -eq 0 ]] ; then
    echo "usage: util/xdebug.sh bin/magento"
    exit 0
fi

parent_path=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )
script_path="$parent_path/get_default_host.sh"
hostname="$($script_path)"

PARAMS=""

for PARAM in "$@"
do
  PARAMS="${PARAMS} \"${PARAM}\""
done

COMMAND="cd /var/www/magento && export PHP_IDE_CONFIG=\"serverName=$hostname\" && php -dxdebug.remote_enable=1 -dxdebug.remote_autostart=1 -dxdebug.remote_host=\`/sbin/ip route|awk '/default/ { print \$3}'\`  $PARAMS";
#echo "$COMMAND";
docker-compose exec cli sudo -u www-data bash -c "$COMMAND"
