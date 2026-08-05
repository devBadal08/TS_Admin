<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayslipResource\Pages;
use App\Filament\Resources\PayslipResource\RelationManagers;
use App\Models\Payslip;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Employee;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;

class PayslipResource extends Resource
{
    protected static ?string $model = Payslip::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'PaySlips';
    protected static ?string $navigationGroup = 'Payroll Management';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Employee Details')
                    ->schema([

                        Grid::make(2)
                            ->schema([

                                Select::make('employee_id')
                                    ->relationship('employee', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, Get $get, Set $set) {

                                        $employee = Employee::find($state);

                                        if (!$employee) {
                                            return;
                                        }

                                        // Salary Structure
                                        $set('basic', $employee->basic_salary);
                                        $set('hra', $employee->hra);
                                        $set('lta', $employee->lta);
                                        $set('special_allowance', $employee->special_allowance);
                                        $set('conveyance_allowance', $employee->conveyance_allowance);
                                        $set('corporate_nps', $employee->corporate_nps);

                                        // Deductions
                                        $set('pf', $employee->pf);
                                        $set('esic', $employee->esic);
                                        $set('nps', $employee->nps);
                                        $set('labour_welfare_fund', $employee->labour_welfare_fund);
                                        $set('professional_tax', $employee->professional_tax);
                                        $set('income_tax', $employee->income_tax);

                                        self::calculateSalary($get, $set);

                                    }),

                                DatePicker::make('salary_month')
                                    ->displayFormat('F Y')
                                    ->native(false)
                                    ->required(),

                            ]),
                    ]),
                    Section::make('Attendance')
                        ->schema([

                            Grid::make(4)
                                ->schema([

                                    TextInput::make('working_days')
                                        ->numeric()
                                        ->default(30),

                                    TextInput::make('paid_days')
                                        ->numeric()
                                        ->default(30)
                                        ->required(),

                                    TextInput::make('leaves')
                                        ->numeric()
                                        ->default(0),

                                    TextInput::make('lop')
                                        ->numeric()
                                        ->default(0),

                                ]),

                        ]),
                    // Hidden fields
                    Hidden::make('basic'),
                    Hidden::make('hra'),
                    Hidden::make('lta'),
                    Hidden::make('special_allowance'),
                    Hidden::make('conveyance_allowance'),
                    Hidden::make('corporate_nps'),
                    Section::make('Deductions')
                        ->schema([

                            Grid::make(3)
                                ->schema([

                                    TextInput::make('pf')->numeric(),

                                    TextInput::make('esic')->numeric(),

                                    TextInput::make('nps')->numeric(),

                                    TextInput::make('labour_welfare_fund')->numeric(),

                                    TextInput::make('professional_tax')->numeric(),

                                    TextInput::make('income_tax')->numeric(),

                                ]),

                        ]),
                    Section::make('Totals')
                        ->schema([

                            Grid::make(3)
                                ->schema([

                                    TextInput::make('gross_salary')
                                        ->numeric()
                                        ->readOnly(),

                                    TextInput::make('total_deduction')
                                        ->numeric()
                                        ->readOnly(),

                                    TextInput::make('net_salary')
                                        ->numeric()
                                        ->readOnly(),

                                ]),
                        ]),
            ]);
    }

    protected static function calculateSalary(Get $get, Set $set): void
    {
        $gross =
            ($get('basic') ?? 0) +
            ($get('hra') ?? 0) +
            ($get('lta') ?? 0) +
            ($get('special_allowance') ?? 0) +
            ($get('conveyance_allowance') ?? 0) +
            ($get('corporate_nps') ?? 0);

        $deduction =
            ($get('pf') ?? 0) +
            ($get('esic') ?? 0) +
            ($get('nps') ?? 0) +
            ($get('labour_welfare_fund') ?? 0) +
            ($get('professional_tax') ?? 0) +
            ($get('income_tax') ?? 0);

        $set('gross_salary', $gross);
        $set('total_deduction', $deduction);
        $set('net_salary', $gross - $deduction);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label('Employee Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('salary_month')
                    ->label('Salary Month')
                    ->date('F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Generated On')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Payslip $record) => route('payslip.download', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListPayslips::route('/'),
            'create' => Pages\CreatePayslip::route('/create'),
            'edit' => Pages\EditPayslip::route('/{record}/edit'),
        ];
    }
}
