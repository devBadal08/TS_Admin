<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsStats extends StatsOverviewWidget
{
    protected ?string $heading = 'Website Traffic Analytics';

    // Optional: Add a description too
    protected ?string $description = 'Real-time visitor and page interaction data';

    protected function getStats(): array
    {
        $totalViews = PageVisit::count();

        $uniqueVisitors = PageVisit::distinct('session_id')
            ->count('session_id');

        $popularPage = PageVisit::select('page')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('page')
            ->orderByDesc('total')
            ->first();

        return [
            Stat::make('Total Page Views', number_format($totalViews))
                ->description('All time global hits')
                ->descriptionIcon('heroicon-m-eye')
                ->color('success'),

            Stat::make('Unique Visitors', number_format($uniqueVisitors))
                ->description('Unique browser sessions')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make(
                'Most Popular Page',
                $popularPage?->page ?? '/'
            )
                ->description(
                    ($popularPage?->total ?? 0) . ' total visits'
                )
                ->descriptionIcon('heroicon-m-fire')
                ->color('danger'),
        ];
    }
}