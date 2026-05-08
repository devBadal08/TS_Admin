<?php

namespace App\Filament\Widgets;

use App\Models\PageVisit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LiveActivePagesTable extends TableWidget
{
    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PageVisit::query()
                    ->selectRaw('MIN(id) as id, page, COUNT(*) as active_users')
                    ->where('last_active', '>=', now()->subMinute())
                    ->groupBy('page')
                    ->orderByDesc('active_users')
            )
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->label('Current Page'),

                Tables\Columns\BadgeColumn::make('active_users')
                    ->label('Active Users Online')
                    ->color('success'),
            ]);
    }
}