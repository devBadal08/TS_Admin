<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstallmentInvoiceResource\Pages;
use App\Filament\Resources\InstallmentInvoiceResource\RelationManagers;
use App\Models\InstallmentInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstallmentInvoiceResource extends Resource
{
    protected static ?string $model = InstallmentInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Installment Invoices';
    protected static ?string $navigationGroup = 'Invoices Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInstallmentInvoices::route('/'),
            'create' => Pages\CreateInstallmentInvoice::route('/create'),
            'edit' => Pages\EditInstallmentInvoice::route('/{record}/edit'),
        ];
    }
}
