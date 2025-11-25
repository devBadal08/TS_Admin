<?php

use Illuminate\Support\Facades\Route;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/admin/invoice/{invoice}/pdf', function (Invoice $invoice) {
    $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
        ->setPaper('A4', 'portrait');

    $cleanInvoiceNo = str_replace(['/', '\\'], '-', $invoice->invoice_no);

    return $pdf->download('Invoice_' . $cleanInvoiceNo . '.pdf');
})->name('invoice.pdf');

Route::get('/receipt/{receipt}', [InvoiceController::class, 'generateReceipt'])
    ->name('payment.receipt');

