<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class PopularPagesTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PageVisit::query()
                    ->selectRaw('MIN(id) as id, page, COUNT(*) as total_views')
                    ->groupBy('page')
                    ->orderByDesc('total_views')
            )
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->label('Page Path')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_views')
                    ->label('Total Views')
                    ->sortable(),
            ]);
    }
}