#!/bin/bash
# Telegram Integration Deployment Script

set -e

REMOTE_HOST="root@72.62.119.39"
REMOTE_PATH="/opt/hamit-tech/aktas-system"
LOCAL_PATH="d:\accounting system web app\aktas-system"

echo "🚀 Starting Telegram Integration Deployment..."

# Copy new config
echo "📋 Copying telegram config..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/config/telegram.php" "${REMOTE_HOST}:${REMOTE_PATH}/config/"

# Copy services
echo "📦 Copying TelegramService..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/app/Services/TelegramService.php" "${REMOTE_HOST}:${REMOTE_PATH}/app/Services/"

# Copy exception handler
echo "⚠️  Copying Exception Handler..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/app/Exceptions/Handler.php" "${REMOTE_HOST}:${REMOTE_PATH}/app/Exceptions/"

# Copy listeners
echo "👁️  Copying Auth Event Listener..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/app/Listeners/AuthEventListener.php" "${REMOTE_HOST}:${REMOTE_PATH}/app/Listeners/"

# Copy middleware
echo "🛡️  Copying Form Error Middleware..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/app/Http/Middleware/HandleFormSubmissionErrors.php" "${REMOTE_HOST}:${REMOTE_PATH}/app/Http/Middleware/"

# Copy logging
echo "📝 Copying Log Handler..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/app/Logging/TelegramLogHandler.php" "${REMOTE_HOST}:${REMOTE_PATH}/app/Logging/"

# Copy providers
echo "🔌 Copying Service Provider..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/app/Providers/TelegramServiceProvider.php" "${REMOTE_HOST}:${REMOTE_PATH}/app/Providers/"

# Copy support
echo "🆘 Copying Support Trait..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/app/Support/TelegramNotifiable.php" "${REMOTE_HOST}:${REMOTE_PATH}/app/Support/"

# Copy .env
echo "🔐 Copying .env configuration..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/.env" "${REMOTE_HOST}:${REMOTE_PATH}/"

# Copy documentation
echo "📚 Copying documentation..."
scp -o StrictHostKeyChecking=no "${LOCAL_PATH}/TELEGRAM_INTEGRATION.md" "${REMOTE_HOST}:${REMOTE_PATH}/"

echo "✅ Files copied successfully!"
echo ""
echo "🐳 Restarting Docker containers..."

ssh -o StrictHostKeyChecking=no "${REMOTE_HOST}" << 'ENDSSH'
cd /opt/hamit-tech
docker compose down
sleep 5
docker compose up -d
sleep 20
docker ps -a --format "table {{.Names}}\t{{.Status}}"
echo ""
echo "📋 Latest logs:"
docker logs hamit-tech --tail 30
ENDSSH

echo ""
echo "✨ Deployment completed!"
echo "Bot will now send notifications to Telegram for all errors and events."
