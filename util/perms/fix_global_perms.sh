#!/usr/bin/env bash

#can be run continously like watch .util/fix_global_perms.sh
CURRENT_FOLDER=$(dirname $(readlink -f "$0"))
TARGET_FOLDER="$CURRENT_FOLDER/../magento"
sudo sudo find "$TARGET_FOLDER" ! -user "$USER" -exec sudo chown "$USER": {} +