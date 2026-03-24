<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $view;
    public $docs;

    public function __construct($view, $docs)
    {
        $this->view = $view;
        $this->docs = $docs;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Automated Report: ' . $this->view->name . ' (v' . $this->view->version . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.report',
            with: [
                'view' => $this->view,
                'docs' => $this->docs,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
