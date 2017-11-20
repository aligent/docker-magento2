#!/usr/bin/env bash

#can be run continously like watch .util/fix_global_perms.sh
CURRENT_FOLDER=$(dirname $(readlink -f "$0"))
TARGET_FOLDER="$CURRENT_FOLDER/../../magento"
#sudo find "$TARGET_FOLDER" ! -user "$USER" -exec sudo chown "$USER": {} +
sudo find "$TARGET_FOLDER" ! -user "$USER" ! -path "$TARGET_FOLDER/magento/generated"  -exec sudo chown "$USER": {} +