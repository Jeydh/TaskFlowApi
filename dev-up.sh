#!/usr/bin/env bash
set -e
echo "Démarrage de la database"
docker compose up -d database
sleep 2
php bin/console cache:clear || true
symfony server:start -d --no-tls --listen-ip=127.0.0.1 --port=8000
echo "   API/Base URL : http://127.0.0.1:8000"
