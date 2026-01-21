<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UberShopItemResource\Pages\CreateUberShopItem;
use App\Filament\Resources\UberShopItemResource\Pages\EditUberShopItem;
use App\Filament\Resources\UberShopItemResource\Pages\ListUberShopItems;
use App\Models\UberShopItem;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UberShopItemResource extends Resource
{
    protected static ?string $model = UberShopItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $modelLabel = 'Shop Item';

    protected static ?string $pluralModelLabel = 'Shop Items';

    protected static ?string $recordTitleAttribute = 'display_name';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Item Selection')
                    ->schema([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Category this item appears under'),
                        Select::make('item_id')
                            ->relationship('item', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->helperText('The game item to sell'),
                    ])
                    ->columns(2),
                Section::make('Pricing & Quantity')
                    ->schema([
                        TextInput::make('uber_cost')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->suffix('Ubers')
                            ->helperText('Price in Ubers'),
                        TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->helperText('Amount player receives per purchase'),
                        TextInput::make('refine_level')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(20)
                            ->default(0)
                            ->helperText('Item refine level (0-20)'),
                        TextInput::make('stock')
                            ->numeric()
                            ->minValue(0)
                            ->placeholder('Unlimited')
                            ->helperText('Leave empty for unlimited stock'),
                    ])
                    ->columns(2),
                Section::make('Display & Availability')
                    ->schema([
                        TextInput::make('display_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                        Toggle::make('enabled')
                            ->default(true)
                            ->helperText('Show item in shop'),
                        Toggle::make('is_xilero')
                            ->label('Available on XileRO')
                            ->default(true),
                        Toggle::make('is_xileretro')
                            ->label('Available on XileRetro')
                            ->default(true),
                    ])
                    ->columns(2),
                Section::make('Statistics')
                    ->schema([
                        TextInput::make('views')
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
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),
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
                    ->formatStateUsing(fn ($state) => $state ?? '∞')
                    ->color(fn ($state) => $state !== null && $state <= 10 ? 'danger' : null),
                IconColumn::make('is_xilero')
                    ->label('XRO')
                    ->boolean()
                    ->alignCenter(),
                IconColumn::make('is_xileretro')
                    ->label('XRT')
                    ->boolean()
                    ->alignCenter(),
                IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('views')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('display_order')
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('enabled')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled'),
                TernaryFilter::make('is_xilero')
                    ->label('XileRO')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No'),
                TernaryFilter::make('is_xileretro')
                    ->label('XileRetro')
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No'),
                Filter::make('low_stock')
                    ->label('Low Stock (≤10)')
                    ->query(fn ($query) => $query->whereNotNull('stock')->where('stock', '<=', 10)),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('toggle')
                    ->icon(fn ($record) => $record->enabled ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->enabled ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['enabled' => ! $record->enabled])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('enable')
                        ->label('Enable Selected')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['enabled' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('disable')
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
            'index' => ListUberShopItems::route('/'),
            'create' => CreateUberShopItem::route('/create'),
            'edit' => EditUberShopItem::route('/{record}/edit'),
        ];
    }
}
