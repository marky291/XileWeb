<?php

namespace App\Filament\Resources\UberShopCategoryResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Shop Items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('uber_cost')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('Ubers'),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                TextInput::make('refine_level')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(20)
                    ->default(0),
                TextInput::make('stock')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Unlimited'),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('enabled')
                    ->default(true),
                Toggle::make('is_xilero')
                    ->label('XileRO')
                    ->default(true),
                Toggle::make('is_xileretro')
                    ->label('XileRetro')
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_order')
                    ->label('#')
                    ->sortable(),
                ViewColumn::make('item_icon')
                    ->label('')
                    ->view('filament.tables.columns.shop-item-icon'),
                TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('uber_cost')
                    ->label('Price')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state.' Ubers'),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('refine_level')
                    ->label('+')
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => $state > 0 ? '+'.$state : '-'),
                TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => $state ?? '∞'),
                IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->filters([
                TernaryFilter::make('enabled'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('display_order');
    }
}
