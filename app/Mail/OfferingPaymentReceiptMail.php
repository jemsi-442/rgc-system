<?php

namespace App\Mail;

use App\Models\OfferingPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferingPaymentReceiptMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public OfferingPayment $payment,
        protected ?string $logoDataUri = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('RGC Payment Receipt - :reference', ['reference' => $this->payment->public_reference]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.offering-payment-receipt',
            with: [
                'payment' => $this->payment,
                'statusUrl' => route('offerings.payments.public.show', $this->payment->public_reference),
                'receiptUrl' => route('offerings.payments.public.receipt', $this->payment->public_reference),
            ],
        );
    }

    public function build(): static
    {
        $pdf = Pdf::loadView('panel.offerings.receipt-pdf', [
            'payment' => $this->payment,
            'logoDataUri' => $this->logoDataUri,
        ])->setPaper('a4');

        return $this
            ->view('emails.offering-payment-receipt')
            ->with([
                'payment' => $this->payment,
                'statusUrl' => route('offerings.payments.public.show', $this->payment->public_reference),
                'receiptUrl' => route('offerings.payments.public.receipt', $this->payment->public_reference),
            ])
            ->attachData(
                $pdf->output(),
                'offering-receipt-' . $this->payment->public_reference . '.pdf',
                ['mime' => 'application/pdf']
            );
    }
}
