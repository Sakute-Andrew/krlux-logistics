<?php

// app/Mail/OrderConfirmed.php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Замовлення #{$this->order->id} підтверджено — KrLux Logistics",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-confirmed',
        );
    }
}

