#!/usr/bin/env bash

#can be run continously like watch .util/fix_global_perms.sh
CURRENT_FOLDER=$(dirname $(readlink -f "$0"))
TARGET_FOLDER="$CURRENT_FOLDER/../../magento/magento"
#translate ../ into actual path for readibility
TARGET_FOLDER=$(readlink -m "$TARGET_FOLDER")
#echo "target folder is $TARGET_FOLDER"
MATCHES=$(sudo find "$TARGET_FOLDER"  ! -user "$USER"  ! -path "$TARGET_FOLDER/vendor*" ! -path "$TARGET_FOLDER/update*" ! -path "$TARGET_FOLDER/pub/static*" ! -path "$TARGET_FOLDER/var*" -print | wc -l)
echo "MATCHES $MATCHES"
if [[ "$MATCHES" == "0" ]]
then
    echo "Nothing to chown"
else
   echo "changing in the batches of 1000"
   find "$TARGET_FOLDER"  ! -user "$USER"  ! -path "$TARGET_FOLDER/vendor*" ! -path "$TARGET_FOLDER/update*" ! -path "$TARGET_FOLDER/pub/static*" ! -path "$TARGET_FOLDER/var*" -print0  | xargs -0 -n 1000 sudo chown "$USER"
fi