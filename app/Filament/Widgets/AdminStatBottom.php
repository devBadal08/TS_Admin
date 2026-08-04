<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

use App\Models\Client;
use App\Models\Gallery;
use App\Models\ProformaInvoice;
use App\Models\PaymentReceipt;
use App\Models\Employee;
use App\Models\Payslip;

class AdminStatBottom extends BaseWidget
{
    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Clients', Client::count())
                ->color('warning')
                ->extraAttributes(['class' => 'stat-card-custom'])
                ->description(new HtmlString('
                    <span class="text-xs text-warning-600">Registered clients</span>
                    <img src="' . asset('images/client.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 7.5rem; height: 7.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\ClientResource::getUrl()),

            Stat::make('Galleries', Gallery::count())
                ->color('success')
                ->extraAttributes(['class' => 'stat-card-custom'])
                ->description(new HtmlString('
                    <span class="text-xs text-success-600">Photo galleries</span>
                    <img src="' . asset('images/gallery.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 7.5rem; height: 7.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\GalleryResource::getUrl()),

            Stat::make('Proforma Invoices', ProformaInvoice::count())
                ->color('primary')
                ->extraAttributes(['class' => 'stat-card-custom'])
                ->description(new HtmlString('
                    <span class="text-xs text-primary-600">Draft invoices</span>
                    <img src="' . asset('images/proforma.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 7.5rem; height: 7.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\ProformaInvoiceResource::getUrl()),

            Stat::make('Payment Receipts', PaymentReceipt::count())
                ->color('success')
                ->extraAttributes(['class' => 'stat-card-custom'])
                ->description(new HtmlString('
                    <span class="text-xs text-success-600">Received payments</span>
                    <img src="' . asset('images/receipt.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 7.5rem; height: 7.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\InstallmentInvoiceResource::getUrl()),

            Stat::make('Employees', Employee::count())
                ->color('warning')
                ->extraAttributes(['class' => 'stat-card-custom'])
                ->description(new HtmlString('
                    <span class="text-xs text-warning-600">Total employees</span>
                    <img src="' . asset('images/employee.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 7.5rem; height: 7.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\EmployeeResource::getUrl()),

            Stat::make('Payslips', Payslip::count())
                ->color('success')
                ->extraAttributes(['class' => 'stat-card-custom'])
                ->description(new HtmlString('
                    <span class="text-xs text-success-600">Generated payslips</span>
                    <img src="' . asset('images/payslip.png') . '" style="position: absolute; right: 1.5rem; top: 50%; transform: translateY(-50%); width: 7.5rem; height: 7.5rem; object-fit: contain;" />
                '))
                ->url(\App\Filament\Resources\PayslipResource::getUrl()),
        ];
    }
}