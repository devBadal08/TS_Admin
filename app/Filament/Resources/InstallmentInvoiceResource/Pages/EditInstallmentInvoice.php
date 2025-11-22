<?php

namespace App\Filament\Resources\InstallmentInvoiceResource\Pages;

use App\Filament\Resources\InstallmentInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInstallmentInvoice extends EditRecord
{
    protected static string $resource = InstallmentInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
