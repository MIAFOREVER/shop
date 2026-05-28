#!/usr/bin/env bash
set -euo pipefail

ENV_FILE="${1:-.env.production}"

if [ ! -f "$ENV_FILE" ]; then
  cp .env.production.example "$ENV_FILE"
  echo "Created $ENV_FILE from .env.production.example"
  echo "Edit $ENV_FILE before running this script again."
  exit 1
fi

set -a
source "$ENV_FILE"
set +a

if [ "${SITE_DOMAIN:-}" = "shop.example.com" ]; then
  echo "Please set SITE_DOMAIN in $ENV_FILE before deploying."
  exit 1
fi

WP_URL="https://${SITE_DOMAIN}"
COMPOSE=(docker compose --env-file "$ENV_FILE" -f docker-compose.prod.yml)

"${COMPOSE[@]}" up -d db wordpress caddy

echo "Waiting for WordPress to accept WP-CLI commands..."
until "${COMPOSE[@]}" run --rm wpcli core version >/dev/null 2>&1; do
  sleep 3
done

if ! "${COMPOSE[@]}" run --rm wpcli core is-installed >/dev/null 2>&1; then
  "${COMPOSE[@]}" run --rm wpcli core install \
    --url="$WP_URL" \
    --title="${WP_TITLE:-Mia Jewelry}" \
    --admin_user="${WP_ADMIN_USER:-admin}" \
    --admin_password="${WP_ADMIN_PASSWORD}" \
    --admin_email="${WP_ADMIN_EMAIL}" \
    --skip-email
fi

"${COMPOSE[@]}" run --rm wpcli option update home "$WP_URL"
"${COMPOSE[@]}" run --rm wpcli option update siteurl "$WP_URL"
"${COMPOSE[@]}" run --rm wpcli plugin install woocommerce --activate
"${COMPOSE[@]}" run --rm wpcli theme activate mia-jewelry
"${COMPOSE[@]}" run --rm wpcli rewrite structure '/%postname%/'
"${COMPOSE[@]}" run --rm wpcli mia seed
"${COMPOSE[@]}" run --rm wpcli rewrite flush

echo "Done. Store: $WP_URL"
echo "Admin: $WP_URL/wp-admin"

