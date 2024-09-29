<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Mail\OrderMail;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        // Check if the user is associated with the order
        if ($event->order->user) {
            Mail::to($event->order->user->email)->send(new OrderMail($event->order, 'customer'));
        }

        // Optionally send an email to the admin
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        Mail::to($adminEmail)->send(new OrderMail($event->order, 'admin'));
    }
}
