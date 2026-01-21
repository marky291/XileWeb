<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UberShopCategoryResource\Pages\CreateUberShopCategory;
use App\Filament\Resources\UberShopCategoryResource\Pages\EditUberShopCategory;
use App\Filament\Resources\UberShopCategoryResource\Pages\ListUberShopCategories;
use App\Filament\Resources\UberShopCategoryResource\RelationManagers\ItemsRelationManager;
use App\Models\UberShopCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UberShopCategoryResource extends Resource
{
    protected static ?string $model = UberShopCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static string|\UnitEnum|null $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Uber Shop';

    protected static ?string $modelLabel = 'Category';

    protected static ?string $pluralModelLabel = 'Categories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Internal identifier for the category'),
                        TextInput::make('display_name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Shown to players (may contain RO color codes like ^FF0000)'),
                        TextInput::make('tagline')
                            ->maxLength(255)
                            ->helperText('Short description shown under category name'),
                        TextInput::make('uber_range')
                            ->maxLength(255)
                            ->placeholder('e.g., 50-500')
                            ->helperText('Price range displayed to players'),
                    ])
                    ->columns(2),
                Section::make('Settings')
                    ->schema([
                        TextInput::make('display_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                        Toggle::make('enabled')
                            ->default(true)
                            ->helperText('Hide category from shop when disabled'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('clean_display_name')
                    ->label('Display Name')
                    ->searchable(query: fn ($query, $search) => $query->where('display_name', 'like', "%{$search}%")),
                TextColumn::make('tagline')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('uber_range')
                    ->label('Price Range')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->sortable(),
                IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->filters([
                TernaryFilter::make('enabled')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('display_order');
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUberShopCategories::route('/'),
            'create' => CreateUberShopCategory::route('/create'),
            'edit' => EditUberShopCategory::route('/{record}/edit'),
        ];
    }
}
