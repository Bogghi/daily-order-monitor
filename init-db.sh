#!/usr/bin/env bash

set -euo pipefail

# Simple workflow:
# 1) Check if the container is up
# 2) If not, echo the container is not running
# 3) Connect to the DB console
# 4) Run the script./

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
COMPOSE_FILE="$SCRIPT_DIR/docker-compose.yml"
DB_SERVICE="mariadb"
SQL_FILE="$SCRIPT_DIR/db/table-definitions.sql"

# 1) Check if the container is up
CID="$(docker compose -f "$COMPOSE_FILE" ps -q "$DB_SERVICE" 2>/dev/null || true)"

# 2) If not, echo the container is not running
if [ -z "$CID" ]; then
  echo "Container '$DB_SERVICE' is not running."
  exit 1
fi

# 3) Connect to the DB console and 4) Run the script
#    Use the env vars defined in the container (MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE)
docker compose -f "$COMPOSE_FILE" exec -T "$DB_SERVICE" \
  sh -c 'mariadb -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' < "$SQL_FILE"

echo "SQL script executed against service '$DB_SERVICE'."
