<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
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
        'customer' => 'array',
        'seller' => 'array',
        'bank_details' => 'array',
        'gst_rate' => 'array',
        'items' => 'array',
        'advancePayment' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($customer) {

            // SELLER
            $customer->seller = [
                'name' => 'TECHSTROTA',
                'address' => '503, Sterling Centre, R C Dutt Road, Alkapuri, Vadodara - 390007',
                'phone' => '+91-81288 40055',
                'email' => 'info@techstrota.com',
            ];

            // BANK DETAILS
            $customer->bank_details = [
                'pan' => 'AAVFT0941Q',
                'bank_name' => 'Kotak Mahindra Bank',
                'branch' => 'Vadodara - Race Course Circle',
                'account' => '1147535073',
                'ifsc' => 'KKBK0000841',
                'gstin' => '24AAVFT0941Q1ZF',
            ];

            // GST RATE
            $gstRate = $customer->gst_rate ?? [];

            if ($customer->gst_type === 'no_gst') {

                $customer->gst_rate = [];

            } elseif ($customer->gst_type === 'cgst_sgst') {

                $customer->gst_rate = [
                    'cgst' => $gstRate['cgst'] ?? 9,
                    'sgst' => $gstRate['sgst'] ?? 9,
                ];

            } elseif ($customer->gst_type === 'igst') {

                $customer->gst_rate = [
                    'igst' => $gstRate['igst'] ?? 18,
                ];

            } else {

                $customer->gst_rate = [];
            }

            // CALCULATE SUBTOTAL
            $items = $customer->items ?? [];

            $subtotal = collect($items)->sum(function ($item) {
                return ($item['qty'] ?? 0) * ($item['rate'] ?? 0);
            });

            // GST
            if ($customer->gst_type === 'no_gst') {

                $total = $subtotal;

            } elseif ($customer->gst_type === 'cgst_sgst') {

                $cgstRate = $customer->gst_rate['cgst'] ?? 0;
                $sgstRate = $customer->gst_rate['sgst'] ?? 0;

                $cgst = ($subtotal * $cgstRate) / 100;
                $sgst = ($subtotal * $sgstRate) / 100;

                $total = $subtotal + $cgst + $sgst;

            } else {

                $igstRate = $customer->gst_rate['igst'] ?? 0;

                $igst = ($subtotal * $igstRate) / 100;

                $total = $subtotal + $igst;
            }

            // DISCOUNT
            $discount = $customer->discount ?? 0;

            $discountAmount = ($total * $discount) / 100;

            $total -= $discountAmount;

            $customer->amount = round(max($total, 0), 2);
        });
    }
}
