<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UberShopItemResource\Pages;
use App\Models\UberShopItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UberShopItemResource extends Resource
{
    protected static ?string $model = UberShopItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $modelLabel = 'Shop Item';

    protected static ?string $pluralModelLabel = 'Shop Items';

    protected static ?string $recordTitleAttribute = 'display_name';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Item Selection')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Category this item appears under'),
                        Forms\Components\Select::make('item_id')
                            ->relationship('item', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('The game item to sell'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Pricing & Quantity')
                    ->schema([
                        Forms\Components\TextInput::make('uber_cost')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('Ubers')
                            ->helperText('Price in Ubers'),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->helperText('Amount player receives per purchase'),
                        Forms\Components\TextInput::make('refine_level')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->default(0)
                            ->helperText('Item refine level (0-20)'),
                        Forms\Components\TextInput::make('stock')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Unlimited')
                            ->helperText('Leave empty for unlimited stock'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Display & Availability')
                    ->schema([
                        Forms\Components\TextInput::make('display_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                        Forms\Components\Toggle::make('enabled')
                            ->default(true)
                            ->helperText('Show item in shop'),
                        Forms\Components\Toggle::make('is_xilero')
                            ->label('Available on XileRO')
                            ->default(true),
                        Forms\Components\Toggle::make('is_xileretro')
                            ->label('Available on XileRetro')
                            ->default(true),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\TextInput::make('views')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Number of times this item has been viewed'),
                    ])
                    ->collapsed()
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
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
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
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
                    ->formatStateUsing(fn ($state) => $state ?? '∞')
                    ->color(fn ($state) => $state !== null && $state <= 10 ? 'danger' : null),
                Tables\Columns\IconColumn::make('is_xilero')
                    ->label('XRO')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('is_xileretro')
                    ->label('XRT')
                    ->boolean()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('enabled')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled'),
                Tables\Filters\TernaryFilter::make('is_xilero')
                    ->label('XileRO')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No'),
                Tables\Filters\TernaryFilter::make('is_xileretro')
                    ->label('XileRetro')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock (≤10)')
                    ->query(fn ($query) => $query->whereNotNull('stock')->where('stock', '<=', 10)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle')
                    ->icon(fn ($record) => $record->enabled ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->enabled ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['enabled' => ! $record->enabled])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('enable')
                        ->label('Enable Selected')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['enabled' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('disable')
                        ->label('Disable Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn ($records) => $records->each->update(['enabled' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->reorderable('display_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUberShopItems::route('/'),
            'create' => Pages\CreateUberShopItem::route('/create'),
            'edit' => Pages\EditUberShopItem::route('/{record}/edit'),
        ];
    }
}
