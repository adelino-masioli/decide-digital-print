<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $pdfPath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pedido #{$this->order->id}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.order',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                     ->as("pedido_{$this->order->id}.pdf")
                     ->withMime('application/pdf'),
        ];
    }
} 