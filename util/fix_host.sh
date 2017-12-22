#!/usr/bin/env bash

parent_path=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )
script_path="$parent_path/code/run.php"
#param="-dxdebug.remote_enable=1 -dxdebug.remote_autostart=1 "
param="";
command="php $param $script_path"

eval "$command"
if [ -e /tmp/docker_mean_bee_needs_sudo.flag ]
then
    eval "sudo $command"
fi