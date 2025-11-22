<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'invoice_date',
        'customer_name',
        'amount',
        'gst_type',
        'gst_rate',
        'items',
        'installments',
        'pdf_path',
        'seller',
        'customer',
        'bank_details',
        'terms',
        'declaration',
        'signatureName',
        'advancePayment',
    ];

    protected $casts = [
        'items' => 'array',
        'installments' => 'array',
        'gst_rate' => 'array',
        'seller' => 'array',
        'customer' => 'array',
        'bank_details' => 'array',
        'invoice_date' => 'date',
        'advancePayment' => 'float',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {

            /* ========== SELLER AUTO ========== */
            $invoice->seller = [
                'name'    => 'TECHSTROTA',
                'address' => '156, 1st Floor, C Tower, K10 Atlantis, Sarabhai Campus, Vadodara - 390007',
                'phone'   => '+91-81288 40055',
                'email'   => 'info@techstrota.com',
            ];

            /* ========== INVOICE NUMBER ========== */
            $year = date('Y');

            $lastInvoice = self::whereYear('created_at', $year)
                ->latest()
                ->first();

            $lastNumber = $lastInvoice
                ? (int) explode('/', $lastInvoice->invoice_no)[1]
                : 0;

            $next = $lastNumber + 1;

            $invoice->invoice_no = 'TS/' . str_pad($next, 2, '0', STR_PAD_LEFT) . '/' . $year;
            $invoice->invoice_date = now();

            // If NO GST selected, save empty gst_rate
            if ($invoice->gst_type === 'no_gst') {
                $invoice->gst_rate = json_encode([]);
            }

            /* ========== CALCULATE TOTAL (AMOUNT) ========== */

            $items = $invoice->items ?? [];

            $subtotal = collect($items)->sum(function ($item) {
                return ($item['qty'] ?? 0) * ($item['rate'] ?? 0);
            });

            $advance = $invoice->advancePayment ?? 0;

            if ($invoice->gst_type === 'no_gst') {
                $total = $subtotal - $advance;
            }

            elseif ($invoice->gst_type === 'cgst_sgst') {
                $cgstRate = $invoice->gst_rate['cgst'] ?? 0;
                $sgstRate = $invoice->gst_rate['sgst'] ?? 0;

                $cgst = ($subtotal * $cgstRate) / 100;
                $sgst = ($subtotal * $sgstRate) / 100;

                $total = $subtotal + $cgst + $sgst - $advance;
            }

            else { // igst
                $igstRate = $invoice->gst_rate['igst'] ?? 0;
                $igst = ($subtotal * $igstRate) / 100;

                $total = $subtotal + $igst - $advance;
            }

            $invoice->amount = round($total, 2);
        });
    }

    public static function generateNextInvoiceNumber(): string
    {
        $year = date('Y');

        $lastInvoice = self::whereYear('created_at', $year)
            ->latest()
            ->first();

        $lastNumber = $lastInvoice
            ? (int) explode('/', $lastInvoice->invoice_no)[1]
            : 0;

        $next = $lastNumber + 1;

        return 'TS/' . str_pad($next, 2, '0', STR_PAD_LEFT) . '/' . $year;
    }
}
