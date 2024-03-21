#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS status_monitor_testing;
    GRANT ALL PRIVILEGES ON \`status_monitor_testing%\`.* TO '$MYSQL_USER'@'%';
EOSQL
