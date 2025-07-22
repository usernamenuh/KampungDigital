<?php

namespace App\Jobs;

use App\Models\Kas;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\KasReminderMail;
use App\Mail\KasApprovedMail;
use App\Mail\KasRejectedMail;

class SendKasEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $kas;
    protected $type;
    protected $additionalData;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(Kas $kas, string $type, array $additionalData = [])
    {
        $this->kas = $kas;
        $this->type = $type;
        $this->additionalData = $additionalData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $kas = $this->kas;
            
            // Check if user has email
            if (!$kas->penduduk || !$kas->penduduk->user || !$kas->penduduk->user->email) {
                Log::warning('Cannot send email: user or email not found', [
                    'kas_id' => $kas->id,
                    'type' => $this->type
                ]);
                return;
            }

            $user = $kas->penduduk->user;
            $email = $user->email;

            // Prepare email data
            $emailData = array_merge([
                'kas_id' => $kas->id,
                'amount' => $kas->jumlah,
                'minggu_ke' => $kas->minggu_ke,
                'tahun' => $kas->tahun,
                'rt_no' => $kas->rt->no_rt ?? '-',
                'rw_no' => $kas->rt->rw->no_rw ?? '-',
                'penduduk_nama' => $kas->penduduk->nama_lengkap ?? 'Tidak diketahui',
                'due_date' => $kas->tanggal_jatuh_tempo,
            ], $this->additionalData);

            // Determine which mail class to use based on notification type
            $mailClass = null;
            switch ($this->type) {
                case 'kas_reminder':
                case 'kas_created':
                    $mailClass = new KasReminderMail($emailData);
                    break;
                case 'kas_approved':
                    $mailClass = new KasApprovedMail($emailData);
                    break;
                case 'kas_rejected':
                    $mailClass = new KasRejectedMail($emailData);
                    break;
                default:
                    Log::warning('Unknown notification type', [
                        'type' => $this->type,
                        'kas_id' => $kas->id
                    ]);
                    return;
            }

            // Send the email
            Mail::to($email)->send($mailClass);

            Log::info('Email notification sent successfully', [
                'kas_id' => $kas->id,
                'type' => $this->type,
                'email' => $email
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'kas_id' => $this->kas->id,
                'type' => $this->type,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw to trigger job retry
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Email notification job failed permanently', [
            'kas_id' => $this->kas->id,
            'type' => $this->type,
            'error' => $exception->getMessage()
        ]);
    }
}


