#!/usr/bin/env bash

parent_path=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )
script_path="$parent_path/code/getDefaultHost.php"
param="";
#param="-dxdebug.remote_enable=1 -dxdebug.remote_autostart=1 "
command="php $param $script_path"
eval "$command"