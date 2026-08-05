<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Invoice;

class InvoiceCard extends Widget
{
    protected static string $view = 'filament.widgets.invoice-card';

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $total = Invoice::count();
        $paid = Invoice::where('payment_status', 1)->count();
        $unpaid = Invoice::where('payment_status', 0)->count();

        return [
            'total' => $total,
            'paid' => $paid,
            'unpaid' => $unpaid,
            'paidAmount' => Invoice::where('payment_status', 1)->sum('amount'),
            'unpaidAmount' => Invoice::where('payment_status', 0)->sum('amount'),
            'paidPercent' => $total ? round(($paid / $total) * 100, 2) : 0,
            'unpaidPercent' => $total ? round(($unpaid / $total) * 100, 2) : 0,
        ];
    }
}
