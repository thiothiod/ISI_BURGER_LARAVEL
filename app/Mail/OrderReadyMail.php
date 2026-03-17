<?php

namespace App\Mail;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Votre commande #{$this->order->reference} est prête ! - ISI BURGER",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-ready',
        );
    }

    public function attachments(): array
    {
        $order = $this->order->load(['user', 'items.product']);
        $pdf   = Pdf::loadView('pdf.invoice', compact('order'));

        return [
            Attachment::fromData(
                fn() => $pdf->output(),
                'facture-' . $this->order->reference . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}