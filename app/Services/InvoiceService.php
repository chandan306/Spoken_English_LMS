<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class InvoiceService
{
    public function createForPayment(Payment $payment, Order $order): Invoice
    {
        $course = $order->course;
        $baseAmount = (float) $course->price;
        $totalAmount = (float) $order->amount;

        return Invoice::firstOrCreate(
            ['order_id' => $order->id],
            [
                'user_id' => $order->user_id,
                'payment_id' => $payment->id,
                'invoice_number' => 'INV-'.strtoupper(Str::random(6)).'-'.$order->id,
                'amount' => $baseAmount,
                'discount_amount' => max($baseAmount - $totalAmount, 0),
                'tax_amount' => 0,
                'total_amount' => $totalAmount,
                'invoice_date' => $payment->paid_at ?? now(),
            ]
        );
    }

    public function generatePdf(Invoice $invoice): string
    {
        $invoice->loadMissing(['user', 'order.course', 'payment']);
        if ($invoice->pdf_path && Storage::disk('local')->exists($invoice->pdf_path)) {
            return $invoice->pdf_path;
        }

        $path = 'invoices/'.$invoice->invoice_number.'.pdf';
        $contents = Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4')
            ->output();

        if (!Storage::disk('local')->put($path, $contents)) {
            throw new RuntimeException('Invoice PDF could not be stored.');
        }

        $invoice->forceFill(['pdf_path' => $path])->save();

        return $path;
    }
}
