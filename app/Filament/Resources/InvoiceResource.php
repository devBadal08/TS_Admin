<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ToggleColumn;
use App\Models\Customer;

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
                ->content(function ($get, $record) {

                    if ($record) {
                        $date = \Carbon\Carbon::parse($get('invoice_date') ?? $record->invoice_date);

                        preg_match('/TS\/INV(\d+)/', $record->invoice_no, $matches);

                        $number = isset($matches[1]) ? (int) $matches[1] : 1;

                        return sprintf(
                            'TS/INV%02d/%02d-%04d',
                            $number,
                            $date->format('m'),
                            $date->format('Y')
                        );
                    }

                    return Invoice::generateNextInvoiceNumber($get('invoice_date'));
                }),

            Forms\Components\DatePicker::make('invoice_date')
                ->label('Invoice Date')
                ->required()
                ->live()
                ->default(now()),

            /* ========== SELECT CUSTOMER / COMPANY ========== */

            Forms\Components\Select::make('selected_customer')
                ->label('Select Customer / Company')
                ->options(function () {
                    return Customer::query()
                        ->get()
                        ->mapWithKeys(function ($customer) {
                            return [
                                $customer->id => $customer->customer['name'] ?? 'Unnamed Customer',
                            ];
                        })
                        ->toArray();
                })
                ->searchable()
                ->preload()
                ->live()
                ->dehydrated(false)
                ->afterStateUpdated(function ($state, callable $set) {

                    if (!$state) {
                        return;
                    }

                    $customer = Customer::find($state);

                    if (!$customer) {
                        return;
                    }

                    /* ================= CUSTOMER ================= */

                    $customerDetails = $customer->customer ?? [];

                    $set('customer.name', $customerDetails['name'] ?? '');
                    $set('customer.mobile', $customerDetails['mobile'] ?? '');
                    $set('customer.address', $customerDetails['address'] ?? '');
                    $set('customer.gstno', $customerDetails['gstno'] ?? '');
                    $set('customer.state_name', $customerDetails['state_name'] ?? '');
                    $set('customer.state_code', $customerDetails['state_code'] ?? '');
                    $set('customer.reference_type', $customerDetails['reference_type'] ?? '');
                    $set('customer.reference_no',$customerDetails['reference_no'] ?? '');

                    /* ================= SELLER ================= */

                    $seller = $customer->seller ?? [];

                    $set('seller.name', $seller['name'] ?? '');
                    $set('seller.address', $seller['address'] ?? '');
                    $set('seller.phone', $seller['phone'] ?? '');
                    $set('seller.email', $seller['email'] ?? '');


                    /* ================= BANK DETAILS ================= */

                    $bank = $customer->bank_details ?? [];

                    $set('bank_details.pan', $bank['pan'] ?? '');
                    $set('bank_details.bank_name', $bank['bank_name'] ?? '');
                    $set('bank_details.branch', $bank['branch'] ?? '');
                    $set('bank_details.account', $bank['account'] ?? '');
                    $set('bank_details.ifsc', $bank['ifsc'] ?? '');
                    $set('bank_details.gstin', $bank['gstin'] ?? '');


                    /* ================= GST ================= */

                    $set('gst_type', $customer->gst_type ?? '');

                    $gstRate = $customer->gst_rate ?? [];

                    $set('gst_rate.cgst', $gstRate['cgst'] ?? null);
                    $set('gst_rate.sgst', $gstRate['sgst'] ?? null);
                    $set('gst_rate.igst', $gstRate['igst'] ?? null);


                    /* ================= ITEMS ================= */

                    $set('items', $customer->items ?? []);


                    /* ================= PAYMENT ================= */

                    $set(
                        'advancePayment',
                        $customer->advancePayment ?? 0
                    );

                    $set(
                        'amount',
                        $customer->amount ?? 0
                    );


                    /* ================= TERMS ================= */

                    $set('terms', $customer->terms ?? '');


                    /* ================= DECLARATION ================= */

                    $set('declaration', $customer->declaration ?? '');


                    /* ================= SIGNATURE ================= */

                    $set(
                        'signatureName',
                        $customer->signatureName ?? ''
                    );


                    /* ================= NOTIFICATION ================= */

                    Notification::make()
                        ->title('Customer details loaded')
                        ->success()
                        ->send();
                })
                ->columnSpanFull(),

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
                        ->label('State Name'),

                    Forms\Components\TextInput::make('customer.state_code')
                        ->label('State Code')
                        ->numeric(),

                        Forms\Components\Select::make('customer.reference_type')
                            ->label('Reference Type')
                            ->options([
                                'reference_no' => 'Reference No',
                                'po_no' => 'PO No',
                                'work_order_no' => 'Work Order No',
                                'order_no' => 'Order No',
                            ])
                            ->placeholder('Select Reference Type')
                            ->live(),

                        Forms\Components\TextInput::make('customer.reference_no')
                            ->label(fn (Forms\Get $get) => match (
                                $get('customer.reference_type')
                            ) {
                                'reference_no' => 'Reference No',
                                'po_no' => 'PO No',
                                'work_order_no' => 'Work Order No',
                                'order_no' => 'Order No',
                                default => 'Reference Number',
                            })
                            ->placeholder('Enter Number')
                            ->maxLength(100)
                            ->required(fn (Forms\Get $get) =>
                                filled($get('customer.reference_type'))
                            ),
                ]),

            /* ========== GST TYPE ========== */
            Forms\Components\Select::make('gst_type')
                ->options([
                    'cgst_sgst' => 'CGST + SGST',
                    'igst'      => 'IGST',
                    'no_gst'    => 'No GST',
                ])
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {

                    if ($state === 'cgst_sgst') {
                        $set('gst_rate.cgst', 9);
                        $set('gst_rate.sgst', 9);
                        $set('gst_rate.igst', null);
                    }

                    if ($state === 'igst') {
                        $set('gst_rate.igst', 18);
                        $set('gst_rate.cgst', null);
                        $set('gst_rate.sgst', null);
                    }

                    if ($state === 'no_gst') {
                        $set('gst_rate.cgst', null);
                        $set('gst_rate.sgst', null);
                        $set('gst_rate.igst', null);
                    }
                }),

            /* ===== GST RATE (JSON) ===== */
            Forms\Components\Hidden::make('gst_rate.cgst'),

            Forms\Components\Hidden::make('gst_rate.sgst'),

            Forms\Components\Hidden::make('gst_rate.igst'),

            /* ========== ITEMS (JSON REPEATER) ========== */
            Forms\Components\Repeater::make('items')
                ->label('Invoice Items')
                ->required()
                ->live()
                ->afterStateUpdated(fn ($set, $get) => self::calculateSubTotal($set, $get))
                ->schema([
                    Forms\Components\TextInput::make('description')
                        ->label('Description')
                        ->required(),

                    Forms\Components\Select::make('hsn_sac')
                        ->label('HSN / SAC')
                        ->options(function () {

                            $defaultCodes = [
                                '998314' => '998314 (IT Design & Development Services)',
                                '998313' => '998313 (IT Consulting & Support Services)',
                                '998316' => '998316 (IT Infrastructure & network Management Services)',
                                '998361' => '998361 (Advertising Services)',
                            ];

                            $savedCodes = Invoice::all()
                                ->pluck('items')
                                ->flatten(1)
                                ->pluck('hsn_sac')
                                ->filter()
                                ->unique()
                                ->mapWithKeys(fn ($code) => [$code => $code])
                                ->toArray();

                            return $defaultCodes + $savedCodes;
                        })
                        ->searchable()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('value')
                                ->label('HSN / SAC Code'),
                        ])
                        ->createOptionUsing(fn (array $data) => $data['value']),

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
                ->columns(2)
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
                ->default(0)
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

            Forms\Components\Select::make('terms_template')
                ->label('Terms Template')
                ->placeholder('Select Template')
                ->options([
                    'standard' => 'Standard',
                    'web development' => 'Web Development',
                    'marketing' => 'Digital Marketing',
                ])
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {

                    $templates = [

                        'standard' => '
                            <p>Payment is due within 7 days from the invoice date.</p>
                            <p>Website hosting and domain registration are valid for one year from the date of activation.</p>
                        ',
                        'web development' => '
                            <p>This invoice is raised for the completed website development project as per the agreed scope of work.</p>
                            <p>Any additional features, modifications, or enhancements requested after project completion will be treated as separate work and billed accordingly.</p>
                        ',
                        'marketing' => '
                            <p>Payment must be made monthly in advance.</p>
                            <p>The package includes 10 social media posts, 6 reels, captions, hashtags, graphic design, video editing, and content planning.</p>
                            <p>Any services requested outside the agreed package will be charged separately.</p>
                        ',
                    ];

                    $set('terms', $templates[$state] ?? '');

                }),

            /* ========== TERMS & CONDITIONS ========== */
            Forms\Components\RichEditor::make('terms')
                ->label('Terms & Conditions')
                ->placeholder('Enter payment terms, conditions, etc.')
                ->required()
                ->columnSpanFull(),

            Forms\Components\Select::make('declaration_template')
                ->label('Declaration Template')
                ->placeholder('Select Declaration')
                ->options([
                    'default' => 'Default',
                    'gst' => 'GST Declaration',
                ])
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {

                    $templates = [

                        'default' => '
                            <p>We hereby declare that the information mentioned in this invoice is true and correct.</p>
                            <p>The services described have been provided as agreed, and the amount shown is payable as per the agreed terms and conditions.</p>
                        ',

                        'gst' => '
                            <p>Certified that the particulars given above are true and correct and the amount indicated represents the actual value of the taxable supply.</p>
                            <p>The services mentioned have been completed and delivered as per the agreed project scope and client requirements</p>
                        ',

                    ];

                    $set('declaration', $templates[$state] ?? '');

                }),

            /* ========== DECLARATION ========== */
            Forms\Components\RichEditor::make('declaration')
                ->label('Declaration')
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
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->customer['name'] ?? ''),
                Tables\Columns\TextColumn::make('amount')->money('INR'),
                Tables\Columns\TextColumn::make('invoice_date')->date('d-m-Y'),
                Tables\Columns\ToggleColumn::make('payment_status')
                    ->label('Payment Status')
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Download pdf')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Invoice $record) => route('invoice.pdf', $record))
                    ->openUrlInNewTab(),
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
