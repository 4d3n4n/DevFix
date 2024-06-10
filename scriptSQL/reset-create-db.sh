#!/bin/bash
set -o allexport

source ./.env.dev.local

docker cp ./scriptSQL/docker-db/01_create.sql db_mysql:/docker-entrypoint-initdb.d/01_create.sql


docker exec -it db_mysql bash -c "mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME < /docker-entrypoint-initdb.d/01_create.sql"
