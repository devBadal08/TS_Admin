<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('paragraph')
                    ->label('Short Description')
                    ->required()
                    ->maxLength(500),
                RichEditor::make('content')
                    ->label('Full Content')
                    ->required(),
                FileUpload::make('image')
                    ->label('Main Image')
                    ->image()
                    ->directory('blogs')
                    ->required(),
                Repeater::make('gallery')
                    ->label('Gallery Images')
                    ->schema([
                        FileUpload::make('url')
                            ->label('Image')
                            ->directory('blogs/gallery')
                            ->image()
                            ->imagePreviewHeight('100')
                            ->maxSize(2048),
                        TextInput::make('desc')
                            ->label('Description')
                            ->maxLength(255),
                    ])
                    ->columnSpan('full'),
                TagsInput::make('tags')
                    ->label('Tags')
                    ->placeholder('Press enter to add tag'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Image')
                    ->width(80)
                    ->height(80),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('paragraph')
                    ->label('Description')
                    ->limit(50),
                TextColumn::make('tags')
                    ->formatStateUsing(fn($state) => is_array($state) ? implode(', ', $state) : $state),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function index()
{
    return BlogResource::collection(Blog::all());
}

    /**
     * Order blogs by latest created_at first (descending)
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('created_at');
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
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}