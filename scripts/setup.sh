#!/usr/bin/env bash
set -euo pipefail

if [ ! -f .env ]; then
  cp .env.example .env
  echo "Created .env from .env.example"
fi

set -a
source .env
set +a

docker compose up -d db wordpress

echo "Waiting for WordPress to accept WP-CLI commands..."
until docker compose run --rm wpcli core is-installed >/dev/null 2>&1; do
  if docker compose run --rm wpcli core version >/dev/null 2>&1; then
    break
  fi
  sleep 3
done

if ! docker compose run --rm wpcli core is-installed >/dev/null 2>&1; then
  docker compose run --rm wpcli core install \
    --url="${WP_URL:-http://localhost:8080}" \
    --title="${WP_TITLE:-Mia Jewelry}" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASSWORD:-change-me-now}" \
    --admin_email="${WP_ADMIN_EMAIL:-admin@example.com}" \
    --skip-email
fi

docker compose run --rm wpcli plugin install woocommerce --activate
docker compose run --rm wpcli theme activate mia-jewelry
docker compose run --rm wpcli rewrite structure '/%postname%/'
docker compose run --rm wpcli mia seed
docker compose run --rm wpcli rewrite flush

echo "Done. Store: ${WP_URL:-http://localhost:8080}"
echo "Admin: ${WP_URL:-http://localhost:8080}/wp-admin"

