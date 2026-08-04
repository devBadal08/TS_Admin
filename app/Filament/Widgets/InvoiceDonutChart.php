<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

class InvoiceDonutChart extends ChartWidget
{
    protected static ?string $heading = 'Invoice Status';

    protected int | string | array $columnSpan = [
        'default' => 1,
        'lg' => 1,
    ];

    protected function getData(): array
    {
        $paid = Invoice::where('payment_status', true)->count();
        $unpaid = Invoice::where('payment_status', false)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Invoices',
                    'data' => [$paid, $unpaid],
                    'backgroundColor' => [
                        '#10B981', // Green
                        '#F59E0B', // Amber
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => [
                'Paid',
                'Unpaid',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '70%',
            'maintainAspectRatio' => false,

            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],

            'scales' => [
                'x' => [
                    'display' => false,
                    'grid' => [
                        'display' => false,
                        'drawBorder' => false,
                    ],
                ],
                'y' => [
                    'display' => false,
                    'grid' => [
                        'display' => false,
                        'drawBorder' => false,
                    ],
                ],
            ],
        ];
    }

    protected function getMaxHeight(): ?string
    {
        return '150px';
    }
}