<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function generateReceipt(Invoice $invoice)
    {
        $installments = collect($invoice->installments ?? []);

        if ($installments->isEmpty()) {
            abort(404, 'No payment found');
        }

        $latestIndex = $installments->count() - 1;
        $payment = $installments[$latestIndex];

        // ✅ If receipt not generated yet, auto create
        if (!isset($payment['receipt_no'])) {
            $payment['receipt_no'] = Invoice::generateNextReceiptNumber();

            $installments[$latestIndex] = $payment;

            $invoice->installments = $installments->values()->all();
            $invoice->save();
        }

        $totalPaid = $installments->sum('amount');
        $remaining = $invoice->amount - $totalPaid;

        $cleanReceipt = str_replace(['/', '\\'], '-', $payment['receipt_no']);

        return Pdf::loadView('invoices.payment_receipt', [
            'invoice'   => $invoice,
            'payment'   => $payment,
            'totalPaid' => $totalPaid,
            'remaining' => $remaining
        ])
        ->setPaper('A4', 'portrait')
        ->download('Receipt-' . $cleanReceipt . '.pdf');
    }
}
