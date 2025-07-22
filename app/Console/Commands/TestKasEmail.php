<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kas;
use App\Models\User;
use App\Jobs\SendKasEmailNotification;
use Illuminate\Support\Facades\Mail;
use App\Mail\KasApprovedMail;
use App\Mail\KasRejectedMail;
use App\Mail\KasReminderMail;
use Illuminate\Support\Facades\Log;

class TestKasEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kas:test-email 
                            {--email= : Email address to send test to}
                            {--type=approved : Type of email (approved, rejected, reminder)}
                            {--sync : Send email synchronously instead of using queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test kas email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $type = $this->option('type');
        $sync = $this->option('sync');

        if (!$email) {
            $email = $this->ask('Enter email address to send test to');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address');
            return 1;
        }

        // Find a kas record with email or create test data
        $kas = Kas::whereHas('penduduk.user', function($query) {
            $query->whereNotNull('email');
        })->first();

        if (!$kas) {
            $this->error('No kas record found with user email. Creating test data...');
            
            // Create test data
            $testData = [
                'kas_id' => 999,
                'amount' => 50000,
                'minggu_ke' => 1,
                'tahun' => 2025,
                'rt_no' => '01',
                'rw_no' => '02',
                'penduduk_nama' => 'Test User',
                'due_date' => now()->addDays(7),
                'days_until_due' => 7,
            ];
        } else {
            $testData = [
                'kas_id' => $kas->id,
                'amount' => $kas->jumlah,
                'minggu_ke' => $kas->minggu_ke,
                'tahun' => $kas->tahun,
                'rt_no' => $kas->rt->no_rt ?? '01',
                'rw_no' => $kas->rt->rw->no_rw ?? '02',
                'penduduk_nama' => $kas->penduduk->nama_lengkap ?? 'Test User',
                'due_date' => $kas->tanggal_jatuh_tempo,
                'days_until_due' => now()->diffInDays($kas->tanggal_jatuh_tempo, false),
            ];
        }

        // Add type-specific data
        switch ($type) {
            case 'approved':
                $testData['payment_method'] = 'Transfer Bank';
                $testData['payment_date'] = now();
                break;
            case 'rejected':
                $testData['rejection_reason'] = 'Bukti pembayaran tidak jelas. Mohon upload ulang dengan foto yang lebih jelas.';
                break;
            case 'reminder':
                $testData['is_new_kas'] = false;
                break;
            default:
                $this->error('Invalid email type. Use: approved, rejected, or reminder');
                return 1;
        }

        $this->info("Testing {$type} email to: {$email}");
        $this->info("Test data: " . json_encode($testData, JSON_PRETTY_PRINT));

        try {
            if ($sync) {
                // Send directly without queue
                $this->info('Sending email synchronously...');
                
                switch ($type) {
                    case 'approved':
                        Mail::to($email)->send(new KasApprovedMail($testData));
                        break;
                    case 'rejected':
                        Mail::to($email)->send(new KasRejectedMail($testData));
                        break;
                    case 'reminder':
                        Mail::to($email)->send(new KasReminderMail($testData));
                        break;
                    default:
                        $this->error('Invalid email type. Use: approved, rejected, or reminder');
                        return 1;
                }
                
                $this->info('✅ Email sent successfully (sync)!');
            } else {
                // Use queue (requires kas model)
                if (!$kas) {
                    $this->error('Queue method requires existing kas record. Use --sync flag for test data.');
                    return 1;
                }
                
                $this->info('Dispatching email job to queue...');
                
                $jobType = $type === 'reminder' ? 'kas_reminder' : "kas_{$type}";
                SendKasEmailNotification::dispatch($kas, $jobType, $testData);
                
                $this->info('✅ Email job dispatched to queue!');
                $this->info('Run "php artisan queue:work" to process the job.');
            }

            // Log the test
            Log::info('Test kas email sent', [
                'type' => $type,
                'email' => $email,
                'sync' => $sync,
                'data' => $testData
            ]);

        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: ' . $e->getMessage());
            Log::error('Test kas email failed', [
                'type' => $type,
                'email' => $email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }
}
