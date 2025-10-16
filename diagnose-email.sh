#!/bin/bash

echo "======================================"
echo "Email Configuration Diagnostic"
echo "======================================"
echo ""

echo "1. Current .env configuration:"
echo "------------------------------"
grep "^MAIL_" .env
echo ""

echo "2. Server process status:"
echo "------------------------------"
SERVER_PID=$(ps aux | grep "php artisan serve" | grep -v grep | awk '{print $2}')
if [ -z "$SERVER_PID" ]; then
    echo "❌ No server running"
else
    echo "✅ Server running (PID: $SERVER_PID)"
    ps aux | grep "artisan serve" | grep -v grep | awk '{print "   Started:", $9}'
fi
echo ""

echo "3. Loaded configuration (from server memory):"
echo "------------------------------"
php artisan tinker --execute="
    echo 'MAIL_MAILER: ' . config('mail.default') . PHP_EOL;
    echo 'MAIL_HOST: ' . config('mail.mailers.smtp.host') . PHP_EOL;
    echo 'MAIL_PORT: ' . config('mail.mailers.smtp.port') . PHP_EOL;
    echo 'MAIL_USERNAME: ' . substr(config('mail.mailers.smtp.username'), 0, 10) . '...' . PHP_EOL;
"
echo ""

echo "4. Recommendation:"
echo "------------------------------"
if [ ! -z "$SERVER_PID" ]; then
    echo "⚠️  Server is running with potentially OLD config!"
    echo "   To fix: Restart the server"
    echo ""
    echo "   Option 1: In the terminal where server is running, press Ctrl+C"
    echo "   Option 2: Run: kill $SERVER_PID"
    echo ""
    echo "   Then start fresh: php artisan serve --host=0.0.0.0"
else
    echo "✅ No server running. Start with: php artisan serve --host=0.0.0.0"
fi

echo "======================================"
