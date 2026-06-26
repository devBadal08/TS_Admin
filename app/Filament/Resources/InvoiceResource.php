<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Final Invoices';
    protected static ?string $navigationGroup = 'Invoices Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([

            /* ========== INVOICE NUMBER ========== */
            Forms\Components\Hidden::make('invoice_no'),

            Forms\Components\Hidden::make('invoice_type')
                ->default('invoice'),

            Forms\Components\Placeholder::make('display_invoice_no')
                ->label('Invoice No')
                ->content(fn ($record) =>
                    $record?->invoice_no ?? Invoice::generateNextInvoiceNumber()
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
                    Forms\Components\TextInput::make('customer.gstno')
                        ->label('GST No')
                        ->maxLength(15),
                    Forms\Components\TextInput::make('customer.state_name')
                        ->label('State Name')
                        ->required(),

                    Forms\Components\TextInput::make('customer.state_code')
                        ->label('State Code')
                        ->numeric()
                        ->required(),
                ]),

            /* ========== BANK DETAILS (JSON) ========== */
            Forms\Components\Fieldset::make('Bank Details')
                ->schema([
                    Forms\Components\TextInput::make('bank_details.account')
                            ->label('Account No')
                            ->default('1147535073')
                            ->disabled()
                            ->dehydrated(true)
                            ->required(),
                        Forms\Components\TextInput::make('bank_details.ifsc')
                            ->label('IFSC')
                            ->default('KKBK0000841')
                            ->disabled()
                            ->dehydrated(true)
                            ->required(),
                        Forms\Components\TextInput::make('bank_details.branch')
                            ->label('Branch')
                            ->default('Vadodara - Race Course Circle')
                            ->disabled()
                            ->dehydrated(true)
                            ->required(),
                        Forms\Components\TextInput::make('bank_details.gstin')
                            ->label('GSTIN')
                            ->default('24AAVFT0941Q1ZF')
                            ->disabled()
                            ->dehydrated(true)
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
                ->live(onBlur: true)
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
                    Forms\Components\TextInput::make('description')
                        ->label('Description')
                        ->required(),

                    Forms\Components\TextInput::make('hsn_sac')
                        ->label('HSN / SAC ')
                        ->required(),

                    Forms\Components\TextInput::make('qty')
                        ->numeric()
                        ->default(1)
                        ->minValue(1)
                        ->required()
                        ->live(onBlur: true),

                    Forms\Components\TextInput::make('rate')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->live(onBlur: true),
                ])
                ->columns(4)
                ->columnSpanFull(),

            /* ========== SUBTOTAL & TOTAL AMOUNT ========== */
            Forms\Components\TextInput::make('subtotal')
                ->label('Subtotal')
                ->disabled()
                ->columnSpanFull(),

            /* ========== DISCOUNT ========== */
            Forms\Components\TextInput::make('discount')
                ->label('Discount (%)')
                ->numeric()
                ->live(onBlur: true)
                ->columnSpan(1)
                ->afterStateUpdated(fn ($set, $get) => self::calculateGrandTotal($set, $get)),

            /* ========== ADVANCE PAYMENT ========== */
            Forms\Components\TextInput::make('advancePayment')
                ->label('Advance Payment')
                ->numeric()
                ->live(onBlur: true)
                ->columnSpan(1)
                ->afterStateUpdated(fn ($set, $get) => self::calculateGrandTotal($set, $get)),

            /* ========== TOTAL AMOUNT ========== */
            Forms\Components\TextInput::make('amount')
                ->label('Amount')
                ->numeric()
                ->disabled()
                ->dehydrated()
                ->columnSpanFull()
                ->required(),

            /* ========== TERMS & CONDITIONS ========== */
            Forms\Components\Textarea::make('terms')
                ->label('Terms & Conditions')
                ->rows(3)
                ->placeholder('Enter payment terms, conditions, etc.')
                ->required()
                ->columnSpanFull(),

            /* ========== DECLARATION ========== */
            Forms\Components\Textarea::make('declaration')
                ->label('Declaration')
                ->rows(3)
                ->placeholder('Enter your declaration statement')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    // SUBTOTAL FROM ITEMS (for live UI update)
    public static function calculateSubTotal($set, $get): void
    {
        $items = $get('items') ?? [];

        $subtotal = collect($items)->sum(function ($item) {
            $qty = floatval($item['qty'] ?? 0);
            $rate = floatval($item['rate'] ?? 0);

            return $qty * $rate;
        });

        // save into subtotal (NOT amount)
        $set('subtotal', round($subtotal, 2));

        self::calculateGrandTotal($set, $get);
    }

    // GRAND TOTAL (for live UI update)
    public static function calculateGrandTotal($set, $get): void
    {
        $subtotal = floatval($get('subtotal') ?: 0);
        $discount = floatval($get('discount') ?: 0);
        //$advance = floatval($get('advancePayment') ?: 0);

        $gstType = $get('gst_type');

        if ($gstType === 'no_gst') {
            $total = $subtotal;
        } elseif ($gstType === 'cgst_sgst') {
            $cgstRate = $get('gst_rate.cgst') ?? 0;
            $sgstRate = $get('gst_rate.sgst') ?? 0;

            $cgst = ($subtotal * $cgstRate) / 100;
            $sgst = ($subtotal * $sgstRate) / 100;

            $total = $subtotal + $cgst + $sgst;
        } else {
            $igstRate = $get('gst_rate.igst') ?? 0;
            $igst = ($subtotal * $igstRate) / 100;

            $total = $subtotal + $igst;
        }

        // Apply Discount
        $discountAmount = ($total * $discount) / 100;
        $total = $total - $discountAmount;

        // Apply Advance Payment (optional)
        //$total = $total - $advance;

        $set('amount', round(max($total, 0), 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('amount')->money('INR'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Download pdf')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Invoice $record) => route('invoice.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('id','desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
