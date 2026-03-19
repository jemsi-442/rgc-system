<?php

namespace App\Mail;

use App\Models\OfferingPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BranchPaymentCompletedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public OfferingPayment $payment)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('New completed branch payment - :reference', ['reference' => $this->payment->public_reference]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.branch-payment-completed',
            with: [
                'payment' => $this->payment,
                'statusUrl' => route('offerings.payments.public.show', $this->payment->public_reference),
                'dashboardUrl' => route('dashboard'),
            ],
        );
    }
}