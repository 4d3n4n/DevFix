#!/bin/bash
set -o allexport

source ./.env.dev.local

docker cp ./scriptSQL/docker-db/02_init.sql db_mysql:/docker-entrypoint-initdb.d/

# Exécution des fichiers SQL d'initialisation
docker exec -it db_mysql bash -c "mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME < /docker-entrypoint-initdb.d/02_init.sql"

