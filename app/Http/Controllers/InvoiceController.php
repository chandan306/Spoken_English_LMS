<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice, InvoiceService $invoices): Response
    {
        $this->authorize('view', $invoice);

        try {
            $path = $invoices->generatePdf($invoice);
        } catch (Throwable $exception) {
            Log::error('Invoice PDF download failed.', [
                'invoice_id' => $invoice->id,
                'exception' => $exception->getMessage(),
            ]);
            abort(500, 'The invoice could not be generated. Please try again later.');
        }

        return response()->download(
            Storage::disk('local')->path($path),
            $invoice->invoice_number.'.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}
