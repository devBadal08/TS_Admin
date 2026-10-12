<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use App\Models\Invoice;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Table;

use Filament\Notifications\Notification;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Customer Details';

    protected static ?string $navigationGroup = 'Invoices Management';

    protected static ?int $navigationSort = 1;


    /*
    |--------------------------------------------------------------------------
    | FORM
    |--------------------------------------------------------------------------
    */

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                /*
                |--------------------------------------------------------------------------
                | CUSTOMER DETAILS
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('Customer Details')
                    ->schema([

                        Forms\Components\TextInput::make('customer.gstno')
                            ->label('GST No')
                            ->maxLength(15)
                            ->live(onBlur: true)
                            ->suffixIcon('heroicon-o-arrow-path')
                            ->afterStateUpdated(function ($state, Set $set) {

                                $gstNo = strtoupper(trim($state));

                                /*
                                |--------------------------------------------------------------------------
                                | GST NUMBER VALIDATION
                                |--------------------------------------------------------------------------
                                */

                                if (strlen($gstNo) !== 15) {
                                    return;
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | GST VERIFY API
                                |--------------------------------------------------------------------------
                                */

                                $gst = app(
                                    \App\Services\GstVerifyService::class
                                )->verify($gstNo);

                                if (! $gst) {

                                    Notification::make()
                                        ->title('GST Number not found')
                                        ->danger()
                                        ->send();

                                    return;
                                }

                                /*
                                |--------------------------------------------------------------------------
                                | AUTO FILL GST DETAILS
                                |--------------------------------------------------------------------------
                                */

                                $set(
                                    'customer.name',
                                    $gst['legal_name'] ?? ''
                                );

                                $set(
                                    'customer.address',
                                    $gst['address'] ?? ''
                                );

                                $set(
                                    'customer.state_name',
                                    $gst['state'] ?? ''
                                );

                                $set(
                                    'customer.state_code',
                                    $gst['state_code'] ?? ''
                                );

                                Notification::make()
                                    ->title('GST Verified Successfully')
                                    ->success()
                                    ->send();
                            }),

                        Forms\Components\TextInput::make('customer.name')
                            ->label('Customer / Company Name')
                            ->required(),

                        Forms\Components\Textarea::make('customer.address')
                            ->label('Address')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('customer.state_name')
                            ->label('State Name'),

                        Forms\Components\TextInput::make('customer.state_code')
                            ->label('State Code'),

                        Forms\Components\TextInput::make('customer.mobile')
                            ->label('Mobile')
                            ->required(),

                        Forms\Components\TextInput::make('customer.email')
                            ->label('Email')
                            ->email(),

                        Forms\Components\Select::make('customer.reference_type')
                            ->label('Select Reference Type')
                            ->placeholder('Select Reference Type')
                            ->options([
                                'reference_no' => 'Reference No.',
                                'po_no' => 'PO No.',
                                'work_order_no' => 'Work Order No.',
                                'order_no' => 'Order No.',
                            ])
                            ->live()
                            ->afterStateUpdated(
                                fn (Set $set) => $set('customer.reference_no', null)
                            ),

                        Forms\Components\TextInput::make('customer.reference_no')
                            ->label(fn (Get $get) => match (
                                $get('customer.reference_type')
                            ) {
                                'reference_no' => 'Reference No.',
                                'po_no' => 'PO No.',
                                'work_order_no' => 'Work Order No.',
                                'order_no' => 'Order No.',
                                default => 'Reference Number',
                            })
                            ->placeholder('Enter Number')
                            ->required(fn (Get $get) =>
                                filled($get('customer.reference_type'))
                            )
                            ->maxLength(100),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | BANK DETAILS
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('Bank Details')
                    ->schema([

                        Forms\Components\KeyValue::make('bank_details')
                            ->label('Bank Details')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->afterStateHydrated(function ($component, $state) {

                                if (blank($state)) {

                                    $component->state([
                                        'Account No' => '1147535073',
                                        'IFSC' => 'KKBK0000841',
                                        'Branch' => 'Vadodara - Race Course Circle',
                                        'GSTIN' => '24AAVFT0941Q1ZF',
                                    ]);

                                }

                            })
                            ->addActionLabel('Add Bank Detail')
                            ->reorderable()
                            ->columnSpanFull(),

                    ]),


                /*
                |--------------------------------------------------------------------------
                | GST TYPE
                |--------------------------------------------------------------------------
                */

                Forms\Components\Select::make('gst_type')
                    ->label('GST Type')
                    ->options([
                        'cgst_sgst' => 'CGST + SGST',
                        'igst'      => 'IGST',
                        'no_gst'    => 'No GST',
                    ])
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {

                        if ($state === 'cgst_sgst') {

                            $set('gst_rate.cgst', 9);
                            $set('gst_rate.sgst', 9);
                            $set('gst_rate.igst', null);

                        } elseif ($state === 'igst') {

                            $set('gst_rate.igst', 18);
                            $set('gst_rate.cgst', null);
                            $set('gst_rate.sgst', null);

                        } elseif ($state === 'no_gst') {

                            $set('gst_rate.cgst', null);
                            $set('gst_rate.sgst', null);
                            $set('gst_rate.igst', null);
                        }

                        // IMPORTANT
                        self::calculateAmount($set, $get);
                    }),

                /*
                |--------------------------------------------------------------------------
                | DEFAULT INVOICE ITEMS
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('Default Invoice Items')
                    ->description(
                        'These items will automatically appear when you select this customer while creating an invoice.'
                    )
                    ->schema([

                        Forms\Components\Repeater::make('items')
                            ->label('Default Invoice Items')
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($set, $get) => self::calculateAmount($set, $get))
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
                                    ->label('Qty')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn ($set, $get) => self::calculateAmount($set, $get)
                                    ),

                                Forms\Components\TextInput::make('rate')
                                    ->label('Rate')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(
                                        fn ($set, $get) => self::calculateAmount($set, $get)
                                    ),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ]),


                /*
                |--------------------------------------------------------------------------
                | ADVANCE PAYMENT
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('Default Payment Settings')
                    ->schema([

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->prefix('₹'),

                        Forms\Components\TextInput::make('advancePayment')
                            ->label('Advance Payment')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Forms\Components\TextInput::make('discount')
                            ->label('Discount (%)')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn ($set, $get) => self::calculateAmount($set, $get)
                            ),

                    ])
                    ->columns(2),

                /*
                |--------------------------------------------------------------------------
                | TERMS TEMPLATE
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('Terms & Conditions')
                    ->schema([

                        Forms\Components\Select::make('terms_template')
                            ->label('Terms Template')
                            ->placeholder('Select Template')
                            ->options([
                                'standard' =>
                                    'Standard',

                                'web development' =>
                                    'Web Development',

                                'marketing' =>
                                    'Digital Marketing',
                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {

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

                                $set(
                                    'terms',
                                    $templates[$state] ?? ''
                                );

                            }),

                        Forms\Components\RichEditor::make('terms')
                            ->label('Terms & Conditions')
                            ->placeholder(
                                'Enter payment terms, conditions, etc.'
                            )
                            ->required()
                            ->columnSpanFull(),
                    ]),

                /*
                |--------------------------------------------------------------------------
                | DECLARATION
                |--------------------------------------------------------------------------
                */

                Forms\Components\Section::make('Declaration')
                    ->schema([

                        Forms\Components\Select::make('declaration_template')
                            ->label('Declaration Template')
                            ->placeholder('Select Declaration')
                            ->options([

                                'default' =>
                                    'Default',

                                'gst' =>
                                    'GST Declaration',

                            ])
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {

                                $templates = [

                                    'default' => '
                                        <p>We hereby declare that the information mentioned in this invoice is true and correct.</p>
                                        <p>The services described have been provided as agreed, and the amount shown is payable as per the agreed terms and conditions.</p>
                                    ',

                                    'gst' => '
                                        <p>Certified that the particulars given above are true and correct and the amount indicated represents the actual value of the taxable supply.</p>
                                        <p>The services mentioned have been completed and delivered as per the agreed project scope and client requirements.</p>
                                    ',

                                ];

                                $set(
                                    'declaration',
                                    $templates[$state] ?? ''
                                );

                            }),

                        Forms\Components\RichEditor::make('declaration')
                            ->label('Declaration')
                            ->placeholder(
                                'Enter your declaration statement'
                            )
                            ->required()
                            ->columnSpanFull(),

                    ]),
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATE DEFAULT AMOUNT
    |--------------------------------------------------------------------------
    */

    public static function calculateAmount($set, $get): void
    {
        $items = $get('items') ?? [];

        // =========================
        // SUBTOTAL
        // =========================

        $subtotal = collect($items)->sum(function ($item) {

            $qty = floatval($item['qty'] ?? 0);
            $rate = floatval($item['rate'] ?? 0);

            return $qty * $rate;
        });

        $set('subtotal', round($subtotal, 2));

        // =========================
        // GST
        // =========================

        $gstType = $get('gst_type');

        if ($gstType === 'no_gst') {

            $total = $subtotal;

        } elseif ($gstType === 'cgst_sgst') {

            $cgstRate = floatval($get('gst_rate.cgst') ?? 0);
            $sgstRate = floatval($get('gst_rate.sgst') ?? 0);

            $cgst = ($subtotal * $cgstRate) / 100;
            $sgst = ($subtotal * $sgstRate) / 100;

            $total = $subtotal + $cgst + $sgst;

        } elseif ($gstType === 'igst') {

            $igstRate = floatval($get('gst_rate.igst') ?? 0);

            $igst = ($subtotal * $igstRate) / 100;

            $total = $subtotal + $igst;

        } else {

            $total = $subtotal;
        }

        // =========================
        // DISCOUNT
        // =========================

        $discount = floatval($get('discount') ?? 0);

        $discountAmount = ($total * $discount) / 100;

        $total -= $discountAmount;

        // =========================
        // FINAL AMOUNT
        // =========================

        $set('amount', round(max($total, 0), 2));
    }

    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')

            ->columns([

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer / Company')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('customer.gstno')
                    ->label('GST No')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Default Amount')
                    ->money('INR'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.reference_type')
                    ->label('Reference Type')
                    ->formatStateUsing(fn (?string $state) => match ($state) {
                        'reference_no' => 'Reference No.',
                        'po_no' => 'PO No.',
                        'work_order_no' => 'Work Order No.',
                        'order_no' => 'Order No.',
                        default => '-',
                    }),

                Tables\Columns\TextColumn::make('customer.reference_no')
                    ->label('Reference Number')
                    ->searchable(),
            ])

            ->actions([

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),

            ])

            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PAGES
    |--------------------------------------------------------------------------
    */

    public static function getPages(): array
    {
        return [

            'index' =>
                Pages\ListCustomers::route('/'),

            'create' =>
                Pages\CreateCustomer::route('/create'),

            'edit' =>
                Pages\EditCustomer::route('/{record}/edit'),

        ];
    }
}