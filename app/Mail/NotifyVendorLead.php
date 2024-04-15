<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotifyVendorLead extends Mailable {
    use Queueable, SerializesModels;

    public $data;
    public function __construct($request_data) {
        $this->data = $request_data;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope() {
        return new Envelope(
            subject: "Received new lead from " . env('APP_NAME'),
            from: env('SMTP2_MAIL_FROM_ADDRESS')
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content() {
        return new Content(
            view: 'mail.notify_vendor_lead',
        );
    }
}
