<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'receipt_no',
        'customer',
        'gst_type',
        'gst_rate',
        'payments',
        'amount',
    ];

    protected $casts = [
        'customer' => 'array',
        'payments' => 'array',
        'gst_rate' => 'array',
        'amount'   => 'float',
    ];

    protected static function booted()
    {
        static::creating(function ($receipt) {

            /* ========== RECEIPT NUMBER ========== */
            if (empty($receipt->receipt_no)) {
                $receipt->receipt_no = self::generateNextReceiptNumber();
            }

        });
    }

    public static function generateNextReceiptNumber(): string
    {
        $month = date('m');   // 11
        $year  = date('Y');   // 2025
        $monthYear = $month . '-' . $year;

        // Get last receipt from the same year
        $lastReceipt = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastReceipt) {
            preg_match('/PR(\d+)/', $lastReceipt->receipt_no, $matches);
            $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
        } else {
            $lastNumber = 0;
        }

        $next = $lastNumber + 1;

        return 'TS/PR' . str_pad($next, 2, '0', STR_PAD_LEFT) . '/' . $monthYear;
    }
}
