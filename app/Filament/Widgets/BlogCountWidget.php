<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Blog;

class BlogCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Blogs', Blog::count()),
        ];
    }
}
// Make sure to register this widget in your Filament dashboard configuration if needed.
// This widget will display the total number of blogs in the Filament dashboard.
// You can customize the label and other properties as needed.              