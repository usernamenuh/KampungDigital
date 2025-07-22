#!/bin/bash

echo "🚀 Starting Queue Worker..."
echo "=========================="

# Check if jobs table exists and is working
echo "📊 Checking database tables..."
php artisan tinker --execute="
echo 'Jobs table: ' . DB::table('jobs')->count() . ' pending jobs' . PHP_EOL;
echo 'Failed jobs: ' . DB::table('failed_jobs')->count() . ' failed jobs' . PHP_EOL;
"

# Check for failed jobs and warn
FAILED_COUNT=$(php artisan tinker --execute="echo DB::table('failed_jobs')->count();" 2>/dev/null | tail -1)

if [ "$FAILED_COUNT" -gt 0 ]; then
    echo "⚠️ Warning: There are failed jobs. Run: php artisan queue:flush"
fi

echo ""
echo "🔄 Starting queue worker..."
echo "Press Ctrl+C to stop"
echo ""

# Start queue worker with verbose output
php artisan queue:work --verbose --tries=3 --timeout=60
