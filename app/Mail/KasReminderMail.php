<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class KasReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $kasData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $kasData)
    {
        $this->kasData = $kasData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Reminder Kas';
        if (isset($this->kasData['days_until_due'])) {
            if ($this->kasData['days_until_due'] > 0) {
                $subject = "Reminder: Kas Jatuh Tempo dalam {$this->kasData['days_until_due']} Hari";
            } else {
                $subject = "Urgent: Kas Sudah Jatuh Tempo";
            }
        }
        
        return new Envelope(
            subject: $subject . ' - RT ' . ($this->kasData['rt_no'] ?? '-'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            html: 'emails.kas-reminder',
            text: 'emails.kas-reminder-text',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
