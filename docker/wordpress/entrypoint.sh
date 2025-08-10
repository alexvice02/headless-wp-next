#!/bin/bash
set -e

if [ -f composer.json ] && [ ! -d vendor ]; then
  composer install
fi

exec "$@"