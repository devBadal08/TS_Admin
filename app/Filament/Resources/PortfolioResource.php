<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PortfolioResource\Pages;
use App\Models\Portfolio;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class PortfolioResource extends Resource
{
    protected static ?string $model = Portfolio::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Website Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')
                ->required(),
            Textarea::make('description')
                ->required(),
            FileUpload::make('image')
                ->image()
                ->label('Main Image')
                ->required(),
            // Gallery section: Repeater with image and description per image
            Repeater::make('gallery')
                ->label('Gallery')
                ->schema([
                    FileUpload::make('url')
                        ->label('Image')
                        ->image()
                        ->directory('gallery')
                        ->required(),
                    TextInput::make('desc')
                        ->label('Description')
                        ->required(),
                ])
                ->collapsible()
                ->addActionLabel('Add Image')
                ->helperText('Add multiple images with a description for each one.')
                ->nullable(),
            TextInput::make('url')
                ->url()
                ->label('Project URL')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable(),
                ImageColumn::make('image')->label('Main Image'),
                TextColumn::make('url')
                    ->label('Project URL')
                    ->url(fn ($record) => $record->url, true),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(), // 👈 Add this line for delete button
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(), // 👈 Optional: bulk delete
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}