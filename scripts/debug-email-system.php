#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Email System Debug\n";
echo "====================\n\n";

try {
    // Check email configuration
    echo "📧 Email Configuration:\n";
    echo "MAIL_MAILER: " . config('mail.default') . "\n";
    echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
    echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
    echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
    echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
    echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

    // Check queue configuration
    echo "🔄 Queue Configuration:\n";
    echo "QUEUE_CONNECTION: " . config('queue.default') . "\n";
    echo "Queue driver: " . config('queue.connections.' . config('queue.default') . '.driver') . "\n\n";

    // Check database tables
    echo "📊 Database Status:\n";
    $jobsCount = DB::table('jobs')->count();
    $failedJobsCount = DB::table('failed_jobs')->count();
    echo "Pending jobs: {$jobsCount}\n";
    echo "Failed jobs: {$failedJobsCount}\n\n";

    // Check kas data
    echo "📋 Kas Data Sample:\n";
    $kasCount = DB::table('kas')->count();
    echo "Total kas records: {$kasCount}\n";
    
    if ($kasCount > 0) {
        $sampleKas = DB::table('kas')
            ->join('penduduks', 'kas.penduduk_id', '=', 'penduduks.id')
            ->join('users', 'penduduks.user_id', '=', 'users.id')
            ->select('kas.id', 'kas.status', 'penduduks.nama_lengkap', 'users.email')
            ->whereNotNull('users.email')
            ->first();
            
        if ($sampleKas) {
            echo "Sample kas with email:\n";
            echo "  ID: {$sampleKas->id}\n";
            echo "  Status: {$sampleKas->status}\n";
            echo "  Name: {$sampleKas->nama_lengkap}\n";
            echo "  Email: {$sampleKas->email}\n";
        } else {
            echo "No kas records with email found\n";
        }
    }
    echo "\n";

    // Check mail classes
    echo "📨 Mail Classes:\n";
    $mailClasses = [
        'KasReminderMail' => 'App\\Mail\\KasReminderMail',
        'KasApprovedMail' => 'App\\Mail\\KasApprovedMail',
        'KasRejectedMail' => 'App\\Mail\\KasRejectedMail',
    ];
    
    foreach ($mailClasses as $name => $class) {
        echo "  {$name}: " . (class_exists($class) ? '✅ Exists' : '❌ Missing') . "\n";
    }
    echo "\n";

    // Check job class
    echo "🔧 Job Classes:\n";
    $jobClass = 'App\\Jobs\\SendKasEmailNotification';
    echo "  SendKasEmailNotification: " . (class_exists($jobClass) ? '✅ Exists' : '❌ Missing') . "\n\n";

    echo "✅ Debug completed!\n";

} catch (Exception $e) {
    echo "❌ Error during debug: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
