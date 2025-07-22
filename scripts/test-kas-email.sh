#!/bin/bash

# Test Kas Email Script
# Usage: ./scripts/test-kas-email.sh [email] [type]

EMAIL=${1:-"test@example.com"}
TYPE=${2:-"approved"}

echo "🧪 Testing Kas Email System"
echo "=========================="
echo "Email: $EMAIL"
echo "Type: $TYPE"
echo ""

# Test email synchronously (direct send)
echo "📧 Testing $TYPE email (sync)..."
php artisan kas:test-email --email="$EMAIL" --type="$TYPE" --sync

if [ $? -eq 0 ]; then
    echo "✅ Sync email test completed!"
else
    echo "❌ Sync email test failed!"
    exit 1
fi

echo ""

# Test email via queue (if kas data exists)
echo "📬 Testing $TYPE email (queue)..."
php artisan kas:test-email --email="$EMAIL" --type="$TYPE"

if [ $? -eq 0 ]; then
    echo "✅ Queue email test completed!"
    echo ""
    echo "🔄 To process the queued email, run:"
    echo "   php artisan queue:work --verbose"
else
    echo "❌ Queue email test failed!"
fi

echo ""
echo "📊 Check queue status:"
php artisan queue:monitor

echo ""
echo "🎯 Test completed!"
echo "Check your email inbox for the test messages."
