<?php

namespace App\Filament\Resources\ProformaInvoiceResource\Pages;

use App\Filament\Resources\ProformaInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProformaInvoice extends CreateRecord
{
    protected static string $resource = ProformaInvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        // ✅ After create, go back to list page
        return $this->getResource()::getUrl('index');
    }
}
