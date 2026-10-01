<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessfulMail extends Mailable implements ShouldQueue, ShouldBeUnique
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice)
    {
        $this->afterCommit();
    }

    public function uniqueId(): string
    {
        return (string) $this->invoice->id;
    }

    public function uniqueFor(): int
    {
        return 86400;
    }

    public function build(): static
    {
        $this->invoice->loadMissing(['user', 'order.course', 'payment']);

        return $this->subject('Payment Successful - Order #'.$this->invoice->order->order_number)
            ->view('emails.payment-successful');
    }

    public function attachments(): array
    {
        return $this->invoice->pdf_path
            ? [Attachment::fromStorageDisk('local', $this->invoice->pdf_path)
                ->as($this->invoice->invoice_number.'.pdf')
                ->withMime('application/pdf')]
            : [];
    }
}
