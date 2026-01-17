<?php

namespace App\Filament\Resources\UberShopCategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Shop Items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('item_id')
                    ->relationship('item', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('uber_cost')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('Ubers'),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->default(1),
                Forms\Components\TextInput::make('refine_level')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(20)
                    ->default(0),
                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->minValue(0)
                    ->placeholder('Unlimited'),
                Forms\Components\TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('enabled')
                    ->default(true),
                Forms\Components\Toggle::make('is_xilero')
                    ->label('XileRO')
                    ->default(true),
                Forms\Components\Toggle::make('is_xileretro')
                    ->label('XileRetro')
                    ->default(true),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('display_order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\ViewColumn::make('item_icon')
                    ->label('')
                    ->view('filament.tables.columns.shop-item-icon'),
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('uber_cost')
                    ->label('Price')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn ($state) => $state.' Ubers'),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('refine_level')
                    ->label('+')
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => $state > 0 ? '+'.$state : '-'),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable()
                    ->alignCenter()
                    ->formatStateUsing(fn ($state) => $state ?? '∞'),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('enabled'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('display_order');
    }
}
