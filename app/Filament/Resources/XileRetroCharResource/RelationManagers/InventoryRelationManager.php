<?php

namespace App\Filament\Resources\XileRetroCharResource\RelationManagers;

use App\Models\Item;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryRelationManager extends RelationManager
{
    protected static string $relationship = 'inventory';

    protected static ?string $title = 'Inventory';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nameid')
            ->modifyQueryUsing(function (Builder $query) {
                return $query->afterQuery(function ($records) {
                    // Bulk load all items in a single query
                    $nameids = $records->pluck('nameid')->unique()->filter()->values();
                    if ($nameids->isEmpty()) {
                        return;
                    }

                    $items = Item::whereIn('item_id', $nameids)
                        ->where('is_xileretro', true)
                        ->get()
                        ->keyBy('item_id');

                    // Inject items into each record's cache
                    foreach ($records as $record) {
                        $record->cachedItem = $items->get($record->nameid);
                        $record->itemLoaded = true;
                    }
                });
            })
            ->columns([
                Tables\Columns\ViewColumn::make('item_icon')
                    ->label('')
                    ->view('filament.tables.columns.item-icon'),
                Tables\Columns\TextColumn::make('item_name')
                    ->label('Item')
                    ->getStateUsing(fn ($record) => $record->item?->name)
                    ->description(fn ($record) => $record->item ? null : "ID: {$record->nameid}"),
                Tables\Columns\TextColumn::make('nameid')
                    ->label('Item ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('amount')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('refine')
                    ->label('+')
                    ->sortable()
                    ->alignCenter()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 10 => 'danger',
                        $state >= 7 => 'warning',
                        $state > 0 => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state) => $state > 0 ? "+{$state}" : '-'),
                Tables\Columns\TextColumn::make('card0')
                    ->label('Card 1')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (int $state) => $state > 0 ? $state : '-'),
                Tables\Columns\TextColumn::make('card1')
                    ->label('Card 2')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (int $state) => $state > 0 ? $state : '-'),
                Tables\Columns\TextColumn::make('card2')
                    ->label('Card 3')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (int $state) => $state > 0 ? $state : '-'),
                Tables\Columns\TextColumn::make('card3')
                    ->label('Card 4')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn (int $state) => $state > 0 ? $state : '-'),
                Tables\Columns\IconColumn::make('favorite')
                    ->label('Fav')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('bound')
                    ->label('Bound')
                    ->boolean()
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\Filter::make('refined')
                    ->label('Refined Items')
                    ->query(fn (Builder $query): Builder => $query->where('refine', '>', 0)),
                Tables\Filters\Filter::make('has_cards')
                    ->label('Has Cards')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->where('card0', '>', 0)
                            ->orWhere('card1', '>', 0)
                            ->orWhere('card2', '>', 0)
                            ->orWhere('card3', '>', 0);
                    })),
                Tables\Filters\Filter::make('favorite')
                    ->label('Favorites')
                    ->query(fn (Builder $query): Builder => $query->where('favorite', 1)),
            ])
            ->actions([])
            ->bulkActions([]);
    }
}
