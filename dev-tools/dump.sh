#!/usr/bin/env bash
# Dump file

TARGET_FILE=$1

echo "---------------------------------------------------------------------"
echo "Dumping file ${TARGET_FILE} ..."
cat ${TARGET_FILE}
echo

exit 0