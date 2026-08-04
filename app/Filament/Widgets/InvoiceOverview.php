<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\Widget;

class InvoiceOverview extends Widget
{
    protected static string $view = 'filament.widgets.invoice-overview';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 0,
    ];

    public function getViewData(): array
    {
        $total = Invoice::count();
        $paid = Invoice::where('payment_status', true)->count();
        $unpaid = Invoice::where('payment_status', false)->count();

        return [
            'total' => $total,
            'paid' => $paid,
            'unpaid' => $unpaid,
            'paidAmount' => Invoice::where('payment_status', true)->sum('amount'),
            'unpaidAmount' => Invoice::where('payment_status', false)->sum('amount'),
        ];
    }
}