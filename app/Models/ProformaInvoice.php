<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    protected $fillable = [
        'proforma_invoice_no',
        'invoice_date',
        'customer',
        'seller',
        'bank_details',
        'gst_type',
        'gst_rate',
        'items',
        'advancePayment',
        'amount',
        'terms',
        'declaration',
        'signatureName',
    ];

    protected $casts = [
        'customer'      => 'array',
        'seller'        => 'array',
        'bank_details'  => 'array',
        'gst_rate'       => 'array',
        'items'          => 'array',
        'amount'         => 'float',
        'advancePayment' => 'float',
    ];

    protected static function booted()
    {
        static::creating(function ($invoice) {

            /* ========== PROFORMA NUMBER ========== */
            if (empty($invoice->proforma_invoice_no)) {
                $invoice->proforma_invoice_no = self::generateNextProformaNumber();
            }

            /* ========== SELLER AUTO ========== */
            $invoice->seller = [
                'name'    => 'TECHSTROTA',
                'address' => '503, Sterling Centre, R C Dutt Road, Alkapuri, Vadodara - 390007',
                'phone'   => '+91-81288 40055',
                'email'   => 'info@techstrota.com',
            ];

            // If NO GST selected, save empty gst_rate
            if ($invoice->gst_type === 'no_gst') {
                $invoice->gst_rate = json_encode([]);
            }
        });
    }

    public static function generateNextProformaNumber(): string
    {
        $month = date('m');   // 11
        $year  = date('Y');   // 2025
        $monthYear = $month . '-' . $year;

        // Get last proforma invoice from the same year
        $lastInvoice = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastInvoice) {
            preg_match('/PI(\d+)/', $lastInvoice->proforma_invoice_no, $matches);
            $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        } else {
            $lastNumber = 0;
        }

        $next = $lastNumber + 1;

        return 'TS/PI' . str_pad($next, 2, '0', STR_PAD_LEFT) . '/' . $monthYear;
    }

    /* For showing in create page (Filament placeholder) */
    public static function previewNextProformaNumber(): string
    {
        return self::generateNextProformaNumber();
    }
}
