#!/usr/bin/env bash
set -e

echo "🛑 Arrêt du serveur Symfony..."
symfony server:stop || true

echo "🛑 Arrêt de la database Docker..."
docker compose stop database

echo "✅ Tout est arrêté"