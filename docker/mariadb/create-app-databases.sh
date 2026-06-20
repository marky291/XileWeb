#!/usr/bin/env bash
# Provisions the secondary game databases for local Sail development.
# Runs once on fresh MariaDB volume init (docker-entrypoint-initdb.d).

/usr/bin/mariadb --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS xilero_main;
    CREATE DATABASE IF NOT EXISTS xilero_log;
    CREATE DATABASE IF NOT EXISTS xileretro_main;
    CREATE DATABASE IF NOT EXISTS xileretro_log;
EOSQL

if [ -n "$MYSQL_USER" ]; then
/usr/bin/mariadb --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    GRANT ALL PRIVILEGES ON \`xilero_main\`.* TO '$MYSQL_USER'@'%';
    GRANT ALL PRIVILEGES ON \`xilero_log\`.* TO '$MYSQL_USER'@'%';
    GRANT ALL PRIVILEGES ON \`xileretro_main\`.* TO '$MYSQL_USER'@'%';
    GRANT ALL PRIVILEGES ON \`xileretro_log\`.* TO '$MYSQL_USER'@'%';
EOSQL
fi
