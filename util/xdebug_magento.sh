#!/usr/bin/env bash


parent_path=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )
script_path="$parent_path/xdebug.sh"
PARAMS=""

for PARAM in "$@"
do
  PARAMS="${PARAMS} \"${PARAM}\""
done
command="$script_path bin/magento ${PARAMS}";
#echo "$command"
eval "$command"