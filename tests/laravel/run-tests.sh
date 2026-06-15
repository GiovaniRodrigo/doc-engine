#!/bin/bash
set -e

# Navigate to the Laravel application directory
cd "$(dirname "$0")"

# Copy env files if they don't exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ ! -f .env.dusk ]; then
    cp .env.dusk.example .env.dusk 2>/dev/null || cp .env .env.dusk
    # Ensure edit middleware and AI settings are present in .env.dusk
    if ! grep -q "DOC_ENGINE_EDIT_MIDDLEWARE" .env.dusk; then
        echo 'DOC_ENGINE_EDIT_MIDDLEWARE="auth,role:admin"' >> .env.dusk
    fi
    if ! grep -q "DOC_ENGINE_AI_ENABLED" .env.dusk; then
        echo 'DOC_ENGINE_AI_ENABLED=true' >> .env.dusk
    fi
    # Force APP_URL to match our test port
    sed -i 's|APP_URL=.*|APP_URL=http://localhost:8001|g' .env.dusk
fi

# Ensure app key is generated
if ! grep -q "APP_KEY=base" .env; then
    php artisan key:generate
fi

# Ensure SQLite database exists
mkdir -p database
touch database/database.sqlite

# Clear any cached configs/routes
php artisan config:clear
php artisan route:clear

# Run migrations and sync documentation
php artisan migrate:fresh --env=dusk --force
php artisan docs:sync --env=dusk

# Download Chrome Driver matching the installed version of Chrome/Chromium
php artisan dusk:chrome-driver --detect

# Start serving the Laravel app in the background
php artisan serve --host=0.0.0.0 --port=8001 --env=dusk &
SERVE_PID=$!

# Wait for serve port to be ready
echo "Waiting for test server to start on port 8001..."
FOR_COUNT=0
until curl -s http://127.0.0.1:8001 > /dev/null; do
  sleep 1
  FOR_COUNT=$((FOR_COUNT+1))
  if [ $FOR_COUNT -gt 30 ]; then
    echo "Test server failed to start within 30 seconds."
    kill $SERVE_PID
    exit 1
  fi
done
echo "Test server is up!"

# Run Dusk tests
php artisan dusk
TEST_RESULT=$?

# Terminate serve process
kill $SERVE_PID

exit $TEST_RESULT
