#!/usr/bin/env bash

# Change working directory to this file's folder
cd "$(dirname "$0")"

echo "Run sql upgrade";
mysql --defaults-extra-file=/etc/mysql/debian.cnf < upgrade.sql;
echo "SQL upgrade script OK";

#echo "Run php upgrade";
#php -f upgrade.php
#echo "PHP upgrade script OK";
