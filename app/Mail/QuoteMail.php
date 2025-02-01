<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Quote $quote,
        public string $pdfPath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Orçamento #{$this->quote->id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.quote',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                     ->as("orcamento_{$this->quote->id}.pdf")
                     ->withMime('application/pdf'),
        ];
    }
} 