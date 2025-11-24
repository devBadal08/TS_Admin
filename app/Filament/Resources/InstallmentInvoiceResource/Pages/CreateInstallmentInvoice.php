<?php

namespace App\Filament\Resources\InstallmentInvoiceResource\Pages;

use App\Filament\Resources\InstallmentInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInstallmentInvoice extends CreateRecord
{
    protected static string $resource = InstallmentInvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        // ✅ After create, go back to list page
        return $this->getResource()::getUrl('index');
    }
}
