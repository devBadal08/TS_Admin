<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Employee Details';
    protected static ?string $navigationGroup = 'Payroll Management';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Employee Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextInput::make('name')
                                    ->required(),

                                TextInput::make('employee_code')
                                    ->label('Employee Code')
                                    ->default(function () {
                                        $lastEmployee = Employee::latest('id')->first();

                                        $nextNumber = 1;

                                        if ($lastEmployee && $lastEmployee->employee_code) {
                                            $nextNumber = (int) substr($lastEmployee->employee_code, 2) + 1;
                                        }

                                        return 'TS' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                                    })
                                    ->disabled()
                                    ->dehydrated()
                                    ->unique(ignoreRecord: true),

                                TextInput::make('email')
                                    ->email(),

                                TextInput::make('phone')
                                    ->tel(),

                                TextInput::make('designation')
                                    ->required(),

                                DatePicker::make('joining_date')
                                    ->required(),

                                Toggle::make('status')
                                    ->default(true),

                            ]),
                    ]),

                Section::make('Government Details')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('pan')
                                    ->label('PAN Number'),

                                TextInput::make('pf_number')
                                    ->label('PF Number'),

                                TextInput::make('uan')
                                    ->label('UAN Number'),

                            ]),

                    ]),

                Section::make('Bank Details')
                    ->schema([

                        KeyValue::make('bank_details')
                            ->keyLabel('Field')
                            ->valueLabel('Value')
                            ->afterStateHydrated(function ($component, $state) {
                                if (blank($state)) {
                                    $component->state([
                                        'Bank Account No' => '',
                                    ]);
                                }
                            })
                            ->addActionLabel('Add Detail'),

                    ]),

                Section::make('Salary Structure')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('basic_salary')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('hra')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('lta')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('special_allowance')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('conveyance_allowance')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('corporate_nps')
                                    ->numeric()
                                    ->default(0),

                            ]),

                    ]),

                Section::make('Default Deductions')
                    ->schema([

                        Grid::make(3)
                            ->schema([

                                TextInput::make('pf')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('esic')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('nps')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('labour_welfare_fund')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('professional_tax')
                                    ->numeric()
                                    ->default(0),

                                TextInput::make('income_tax')
                                    ->numeric()
                                    ->default(0),

                            ]),

                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('employee_code')->sortable()->searchable(),
                TextColumn::make('joining_date')->date('d-m-y'),
                TextColumn::make('basic_salary')->sortable()->searchable(),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
