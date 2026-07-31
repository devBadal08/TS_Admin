<?php

use Illuminate\Support\Facades\Route;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProformaInvoiceController;
use App\Services\GstVerifyService;
use App\Http\Controllers\PayslipController;

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

Route::get('/proforma/{id}/download', [ProformaInvoiceController::class, 'download'])
    ->name('proforma.download');

Route::get('/gst-test', function (GstVerifyService $gst) {

    $response = $gst->verify('24AAVFT0941Q1ZF');

    dd([
        
        'Headers' => $response->headers(),
        'Body' => $response->body(),
    ]);

});

Route::get(
    '/payslip/{payslip}/download',
    [PayslipController::class, 'download']
)->name('payslip.download');