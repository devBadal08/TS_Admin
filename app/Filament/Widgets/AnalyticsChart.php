<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AnalyticsChart extends ChartWidget
{
    protected static ?string $heading = 'Website Analytics Overview';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];
    protected static ?string $pollingInterval = '60s';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $analytics = PageVisit::selectRaw("
                DATE(created_at) as day,
                COUNT(*) as page_views,
                COUNT(DISTINCT ip_address) as visitors
            ")
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->groupByRaw("DATE(created_at)")
            ->orderBy("day")
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Visitors',
                    'data' => $analytics->pluck('visitors')->toArray(),
                    'borderColor' => '#2563EB',
                    'backgroundColor' => 'rgba(37,99,235,.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Page Views',
                    'data' => $analytics->pluck('page_views')->toArray(),
                    'borderColor' => '#F97316',
                    'backgroundColor' => 'rgba(249,115,22,.15)',
                    'tension' => 0.4,
                ],
            ],

            'labels' => $analytics
                ->map(fn ($item) => Carbon::parse($item->day)->format('M d'))
                ->toArray(),
        ];
    }

    
}