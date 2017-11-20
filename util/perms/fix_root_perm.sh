#!/usr/bin/env bash

#can be run continously like watch ./util/fix_perm.sh
CURRENT_FOLDER=$(dirname $(readlink -f "$0"))
TARGET_FOLDER="$CURRENT_FOLDER/../../magento"
ls -la "$TARGET_FOLDER" | grep magento
#need permission of
sudo chown "$USER":"$USER" "$TARGET_FOLDER/magento"
ls -la "$TARGET_FOLDER" | grep magento