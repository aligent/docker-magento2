#!/usr/bin/env bash


parent_path=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )
script_path="$parent_path/xdebug.sh"

command="$script_path bin/magento $@";
#echo "$command"
eval "$command"