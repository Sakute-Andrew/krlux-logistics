<?php

// app/Mail/OrderCreated.php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Замовлення #{$this->order->id} прийнято — KrLux Logistics",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-created',
        );
    }
}
