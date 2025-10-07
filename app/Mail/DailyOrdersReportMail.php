<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyOrdersReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $count;
    public $date;

    /**
     * Create a new message instance.
     */
    public function __construct($count, $date)
    {
        $this->count = $count;
        $this->date = $date;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📊 Daily Orders Report',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: "
                <h2>Daily Orders Report</h2>
                <p><strong>Date:</strong> {$this->date}</p>
                <p><strong>Total Orders:</strong> {$this->count}</p>
                <p>This is an automated report from the system.</p>
            ",
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