<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use MailerSend\LaravelDriver\MailerSendTrait;
use MailerSend\Helpers\Builder\Personalization;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;

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

    public function build()
    {
        $to = Arr::get($this->to, '0.address'); // Obtém o destinatário

        return $this
            ->view('mail.order') // Define a view do email
            ->attach($this->pdfPath, [
                'as' => "pedido_{$this->order->id}.pdf",
                'mime' => 'application/pdf',
            ])
            ->mailersend(
                template_id: null, // Se tiver um template do MailerSend, pode definir aqui
                tags: ['order', 'purchase'],
                personalization: [
                    new Personalization($to, [
                        'order_id' => $this->order->id,
                        'customer_name' => $this->order->customer_name,
                        'total' => number_format($this->order->total, 2, ',', '.'),
                    ])
                ],
                precedenceBulkHeader: true
            );
    }
}
