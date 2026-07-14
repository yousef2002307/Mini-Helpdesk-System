#!/bin/sh
set -e

# Run database migrations on startup (safe — skips if already run)
php artisan migrate --force

# Start Vite dev server in the background
npm run dev &

# Start PHP's built-in server in the foreground (keeps container alive)
exec php artisan serve --host=0.0.0.0 --port=8000
