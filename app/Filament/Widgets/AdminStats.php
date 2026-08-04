<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

use App\Models\Blog;
use App\Models\Contact;
use App\Models\Portfolio;

class AdminStats extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'TechStrota Insights';

    protected ?string $description =
        'Monitor website activity, business growth and customer interactions.';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [

            Stat::make('Blogs', Blog::count())
                ->color('primary')
                ->description(new HtmlString('
                    <span class="text-xs text-primary-600">Total blog posts</span>
                    <img src="' . asset('images/blog.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 6.5rem; height: 6.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\BlogResource::getUrl()),

            Stat::make('Contacts', Contact::count())
                ->color('success')
                ->description(new HtmlString('
                    <span class="text-xs text-success-600">Customer inquiries</span>
                    <img src="' . asset('images/contact.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 6.5rem; height: 6.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\ContactResource::getUrl()),

            Stat::make('Portfolios', Portfolio::count())
                ->color('info')
                ->description(new HtmlString('
                    <span class="text-xs text-info-600">Projects showcase</span>
                    <img src="' . asset('images/portfolio.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 6.5rem; height: 6.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\PortfolioResource::getUrl()),

        ];
    }
}