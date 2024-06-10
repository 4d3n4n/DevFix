#!/bin/bash
set -o allexport

print_error() {
  echo -e "\e[91mError: $1\e[0m" >&2
}

wait_for_mysql() {
  local max_attempts=10
  local attempt=1
  echo "Waiting for db_mysql container to be ready..."
  while [ $attempt -le $max_attempts ]; do
    if docker container inspect db_mysql >/dev/null 2>&1; then
      echo "db_mysql container is running."
      return 0
    else
      echo "Attempt $attempt: db_mysql container is not running yet. Retrying in 20 seconds..."
      sleep 15
      ((attempt++))
    fi
  done
  echo "Error: Timeout reached. db_mysql container is not running."
  return 1
}


if ! [ -x "$(command -v composer)" ]; then
  print_error "Composer is not installed."
  exit 1
fi

if ! [ -x "$(command -v symfony)" ]; then
  print_error "Symfony CLI is not installed."
  exit 1
fi

if ! [ -x "$(command -v php)" ]; then
  print_error "PHP is not installed."
  exit 1
fi

if ! [ -x "$(command -v docker)" ]; then
  print_error "Docker is not installed."
  exit 1
fi

if ! [ -x "$(command -v docker compose)" ]; then
  print_error "Docker Compose is not installed."
  exit 1
fi

composer install


echo "Starting Docker containers..."
docker-compose up --build -d
sleep 5
echo "🟢 Success"

wait_for_mysql || echo "Mysql is not running"

echo -e "\033[96mCreating database with test data...\033[0m"
echo -e "\033[96mInitializing database with test data...\033[0m"
echo -e "🟢 Success database is created & initialized"


echo "open localhost:7070 to check initalization"
sleep 10

  echo "Starting Symfony application..."
  symfony server:start