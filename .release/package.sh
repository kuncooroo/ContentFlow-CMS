#!/usr/bin/env bash
set -euo pipefail

VERSION="1.0.0"
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DIST="$ROOT/dist"
NAME="contentflow-cms-${VERSION}"
ARCHIVE="$DIST/${NAME}.tar.gz"

cd "$ROOT"
mkdir -p "$DIST"

# Build production front-end assets when npm is available.
if command -v npm >/dev/null 2>&1; then
  npm ci
  npm run build
fi

# Create tarball excluding dev artifacts and secrets.
tar \
  --exclude='.git' \
  --exclude='.env' \
  --exclude='.env.*' \
  --exclude='!.env.example' \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='storage/logs' \
  --exclude='storage/app/install.lock' \
  --exclude='storage/framework/cache' \
  --exclude='storage/framework/sessions' \
  --exclude='storage/framework/views' \
  --exclude='storage/framework/testing' \
  --exclude='public/hot' \
  --exclude='.phpunit.result.cache' \
  --exclude='.idea' \
  --exclude='.vscode' \
  --exclude='dist' \
  -czf "$ARCHIVE" \
  -C "$ROOT" \
  app bootstrap config database docs public resources routes tests \
  .env.example artisan composer.json composer.lock package.json package-lock.json \
  vite.config.js CHANGELOG.md LICENSE.md README.md .release

echo "Created $ARCHIVE"
echo "Verify: tar -tzf $ARCHIVE | head"
