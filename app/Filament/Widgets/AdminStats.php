<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use App\Models\Blog;
use App\Models\Contact;
use App\Models\Portfolio;
use App\Models\Client;
use App\Models\Gallery;
use App\Models\Invoice;
use App\Models\ProformaInvoice;
use App\Models\PaymentReceipt;

class AdminStats extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [

            Stat::make('Blogs', Blog::count())
                ->description('Total blog posts')
                ->icon('heroicon-o-pencil-square')
                ->color('primary')
                ->url(\App\Filament\Resources\BlogResource::getUrl()),

            Stat::make('Contacts', Contact::count())
                ->description('Customer inquiries')
                ->icon('heroicon-o-envelope')
                ->color('success')
                ->url(\App\Filament\Resources\ContactResource::getUrl()),

            Stat::make('Portfolios', Portfolio::count())
                ->description('Projects showcase')
                ->icon('heroicon-o-briefcase')
                ->color('info')
                ->url(\App\Filament\Resources\PortfolioResource::getUrl()),

            Stat::make('Clients', Client::count())
                ->description('Registered clients')
                ->icon('heroicon-o-users')
                ->color('warning')
                ->url(\App\Filament\Resources\ClientResource::getUrl()),

            Stat::make('Galleries', Gallery::count())
                ->description('Photo galleries')
                ->icon('heroicon-o-photo')
                ->color('success')
                ->url(\App\Filament\Resources\GalleryResource::getUrl()),

            Stat::make('Invoices', Invoice::count())
                ->description('Total invoices')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->url(\App\Filament\Resources\InvoiceResource::getUrl()),

            Stat::make('Proforma Invoices', ProformaInvoice::count())
                ->description('Draft invoices')
                ->icon('heroicon-o-clipboard-document')
                ->color('primary')
                ->url(\App\Filament\Resources\ProformaInvoiceResource::getUrl()),

            Stat::make('Payment Receipts', PaymentReceipt::count())
                ->description('Received payments')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->url(\App\Filament\Resources\InstallmentInvoiceResource::getUrl()),

        ];
    }
}
