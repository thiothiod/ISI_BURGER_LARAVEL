<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;
    // Le constructeur de la classe prend une instance de la commande
    // et la stocke dans une propriété publique pour être utilisée dans les méthodes envelope() et content().
    //le use Queueable et SerializesModels sont des traits fournis par Laravel pour permettre à la 
    //classe de mail d'être mise en file d'attente et de sérialiser les modèles Eloquent correctement 
    //lorsqu'ils sont passés à la classe de mail.

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    //la méthode envelope() définit l'enveloppe du mail, notamment le sujet. Ici, le sujet inclut la référence de la commande pour une identification facile.
    {
        return new Envelope(
            subject: "🍔 Nouvelle commande #{$this->order->reference} reçue !",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-order-notification',
        );
    }
}