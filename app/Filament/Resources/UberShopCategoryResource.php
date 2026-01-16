<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UberShopCategoryResource\Pages;
use App\Filament\Resources\UberShopCategoryResource\RelationManagers;
use App\Models\UberShopCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UberShopCategoryResource extends Resource
{
    protected static ?string $model = UberShopCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Uber Shop';

    protected static ?string $modelLabel = 'Category';

    protected static ?string $pluralModelLabel = 'Categories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Internal identifier for the category'),
                        Forms\Components\TextInput::make('display_name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Shown to players (may contain RO color codes like ^FF0000)'),
                        Forms\Components\TextInput::make('tagline')
                            ->maxLength(255)
                            ->helperText('Short description shown under category name'),
                        Forms\Components\TextInput::make('uber_range')
                            ->maxLength(255)
                            ->placeholder('e.g., 50-500')
                            ->helperText('Price range displayed to players'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('display_order')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                        Forms\Components\Toggle::make('enabled')
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
                Tables\Columns\TextColumn::make('display_order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clean_display_name')
                    ->label('Display Name')
                    ->searchable(query: fn ($query, $search) => $query->where('display_name', 'like', "%{$search}%")),
                Tables\Columns\TextColumn::make('tagline')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('uber_range')
                    ->label('Price Range')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->sortable(),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('display_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('enabled')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('display_order');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUberShopCategories::route('/'),
            'create' => Pages\CreateUberShopCategory::route('/create'),
            'edit' => Pages\EditUberShopCategory::route('/{record}/edit'),
        ];
    }
}
