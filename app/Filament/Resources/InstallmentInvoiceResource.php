<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstallmentInvoiceResource\Pages;
use App\Filament\Resources\InstallmentInvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstallmentInvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Payment Receipts';
    protected static ?string $navigationGroup = 'Invoices Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                /* ========== INVOICE NUMBER ========== */
                Forms\Components\Placeholder::make('receipt_no')
                    ->label('Payment Receipt No')
                    ->content(fn ($record) =>
                        collect($record?->installments ?? [])->last()['receipt_no']
                        ?? \App\Models\Invoice::generateNextReceiptNumber()
                    ),

                Forms\Components\DatePicker::make('invoice_date')
                    ->label('Invoice Date')
                    ->required()
                    ->default(now()),

                /* ========== CUSTOMER DETAILS (JSON) ========== */
                Forms\Components\Fieldset::make('Customer Details')
                    ->schema([
                        Forms\Components\TextInput::make('customer.name')->required(),
                        Forms\Components\TextInput::make('customer.mobile')->required(),
                        Forms\Components\Textarea::make('customer.address')->required(),
                    ]),

                /* ========== BANK DETAILS (JSON) ========== */
                Forms\Components\Fieldset::make('Bank Details')
                    ->schema([
                        Forms\Components\TextInput::make('bank_details.account')
                            ->label('Account No')
                            ->default('1147535073')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\TextInput::make('bank_details.ifsc')
                            ->label('IFSC')
                            ->default('KKBK0000841')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\TextInput::make('bank_details.branch')
                            ->label('Branch')
                            ->default('Vadodara - Race Course Circle')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                    ]),

                /* ========== GST TYPE ========== */
                Forms\Components\Select::make('gst_type')
                    ->options([
                        'cgst_sgst' => 'CGST + SGST',
                        'igst' => 'IGST',
                        'no_gst' => 'No GST',
                    ])
                    ->required()
                    ->reactive()
                    ->columnSpanFull(),

                /* ===== GST RATE (JSON) ===== */
                Forms\Components\Group::make()
                    ->visible(fn ($get) => $get('gst_type') === 'cgst_sgst')
                    ->schema([
                        Forms\Components\TextInput::make('gst_rate.cgst')->label('CGST %')->numeric(),
                        Forms\Components\TextInput::make('gst_rate.sgst')->label('SGST %')->numeric(),
                    ]),

                Forms\Components\TextInput::make('gst_rate.igst')
                    ->label('IGST %')
                    ->numeric()
                    ->visible(fn ($get) => $get('gst_type') === 'igst'),

                /* ========== ITEMS (JSON REPEATER) ========== */
                Forms\Components\Repeater::make('items')
                    ->label('Invoice Items')
                    ->required()
                    ->schema([
                        Forms\Components\TextInput::make('description'),

                        Forms\Components\TextInput::make('qty')
                            ->numeric()
                            ->default(1)
                            ->reactive(),

                        Forms\Components\TextInput::make('rate')
                            ->numeric()
                            ->reactive(),
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn ($set, $get) => self::calculateSubTotal($set, $get))
                    ->columns(3)
                    ->columnSpanFull(),

                /* ========== SUBTOTAL & TOTAL AMOUNT ========== */
                Forms\Components\TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->disabled()
                    ->columnSpanFull(),

                /* ========== ADVANCE PAYMENT ========== */
                Forms\Components\TextInput::make('advancePayment')
                    ->label('Advance Payment')
                    ->numeric()
                    ->reactive()
                    ->required()
                    ->columnSpanFull()
                    ->afterStateUpdated(fn ($set, $get) => self::calculateGrandTotal($set, $get)),

                Forms\Components\Repeater::make('installments')
                    ->label('Installment Payments')
                    ->schema([
                        Forms\Components\TextInput::make('receipt_no')
                            ->label('Receipt No')
                            ->default(fn () => \App\Models\Invoice::generateNextReceiptNumber())
                            ->disabled()
                            ->dehydrated()
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required(),

                        Forms\Components\DatePicker::make('date')
                            ->required(),
                    ])
                    ->columnSpanFull(),

                /* ========== TOTAL AMOUNT ========== */
                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    // SUBTOTAL FROM ITEMS (for live UI update)
    public static function calculateSubTotal($set, $get): void
    {
        $items = $get('items') ?? [];

        $subtotal = collect($items)->sum(function ($item) {
            return ($item['qty'] ?? 0) * ($item['rate'] ?? 0);
        });

        // save into subtotal (NOT amount)
        $set('subtotal', round($subtotal, 2));

        self::calculateGrandTotal($set, $get);
    }

    // GRAND TOTAL (for live UI update)
    public static function calculateGrandTotal($set, $get): void
    {
        $subtotal = $get('subtotal') ?? 0;
        $advance  = $get('advancePayment') ?? 0;

        $gstType = $get('gst_type');

        if ($gstType === 'no_gst') {
            $total = $subtotal - $advance;
        } elseif ($gstType === 'cgst_sgst') {
            $cgstRate = $get('gst_rate.cgst') ?? 0;
            $sgstRate = $get('gst_rate.sgst') ?? 0;

            $cgst = ($subtotal * $cgstRate) / 100;
            $sgst = ($subtotal * $sgstRate) / 100;

            $total = $subtotal + $cgst + $sgst - $advance;
        } else { // igst
            $igstRate = $get('gst_rate.igst') ?? 0;
            $igst = ($subtotal * $igstRate) / 100;

            $total = $subtotal + $igst - $advance;
        }

        $set('amount', round($total, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_no')
                    ->label('Receipt No')
                    ->getStateUsing(function ($record) {

                        $installments = is_string($record->installments)
                            ? json_decode($record->installments, true)
                            : $record->installments;

                        $last = collect($installments)->last();

                        return $last['receipt_no'] ?? '-';
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('amount')->money('INR'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Payment Receipt')
                    ->icon('heroicon-o-receipt-refund')
                    ->color('success')
                    ->url(fn (Invoice $record) => route('payment.receipt', $record))
                    ->openUrlInNewTab(),
            ])
            ->defaultSort('id','desc')
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('installments')
            ->where('installments', '!=', '[]');
    }
}
