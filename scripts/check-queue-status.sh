#!/bin/bash

echo "📊 Queue Status Check"
echo "===================="

# Check database tables
echo "📋 Database Status:"
php artisan tinker --execute="
echo 'Jobs table: ' . DB::table('jobs')->count() . ' pending' . PHP_EOL;
echo 'Failed jobs: ' . DB::table('failed_jobs')->count() . ' failed' . PHP_EOL;
"

echo ""

# Check recent jobs
echo "📝 Recent Jobs:"
php artisan tinker --execute="
\$recentJobs = DB::table('jobs')->orderBy('created_at', 'desc')->limit(5)->get(['id', 'queue', 'payload', 'attempts', 'created_at']);
if (\$recentJobs->count() > 0) {
    foreach (\$recentJobs as \$job) {
        echo 'Job ID: ' . \$job->id . ' | Queue: ' . \$job->queue . ' | Attempts: ' . \$job->attempts . PHP_EOL;
    }
} else {
    echo 'No pending jobs' . PHP_EOL;
}
"

echo ""

# Check failed jobs
echo "❌ Failed Jobs:"
php artisan tinker --execute="
\$failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->limit(3)->get(['id', 'connection', 'queue', 'exception', 'failed_at']);
if (\$failedJobs->count() > 0) {
    foreach (\$failedJobs as \$job) {
        echo 'Failed Job ID: ' . \$job->id . ' | Queue: ' . \$job->queue . PHP_EOL;
        echo 'Error: ' . substr(\$job->exception, 0, 100) . '...' . PHP_EOL;
        echo '---' . PHP_EOL;
    }
} else {
    echo 'No failed jobs' . PHP_EOL;
}
"

echo ""
echo "🔧 Commands:"
echo "  Clear failed jobs: php artisan queue:flush"
echo "  Start worker: php artisan queue:work --verbose"
echo "  Monitor queue: watch -n 2 'php artisan queue:monitor'"
