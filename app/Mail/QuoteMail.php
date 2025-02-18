<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\LaravelDriver\MailerSendTrait;
use Illuminate\Mail\Mailables\Attachment;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;

    public function __construct(
        public Quote $quote,
        public string $pdfPath
    ) {}

    public function build()
    {
        $to = Arr::get($this->to, '0.address');

        return $this
            ->view('mail.quote')
            ->attach(Attachment::fromPath($this->pdfPath)
                ->as("orcamento_{$this->quote->id}.pdf")
                ->withMime('application/pdf'))
            ->mailersend(
                template_id: null, 
                tags: ['quote'],
                personalization: [
                    new Personalization($to, [
                        'quote_id' => $this->quote->id,
                        'customer_name' => $this->quote->customer->name ?? 'Cliente',
                        'total_price' => number_format($this->quote->total, 2, ',', '.')
                    ])
                ]
            );
    }
}
