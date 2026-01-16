<?php

namespace App\Filament\Resources;

use App\Filament\Resources\XileRetroInventoryResource\Pages\CreateXileRetroInventory;
use App\Filament\Resources\XileRetroInventoryResource\Pages\EditXileRetroInventory;
use App\Filament\Resources\XileRetroInventoryResource\Pages\ListXileRetroInventories;
use App\XileRetro\XileRetro_Inventory as Inventory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class XileRetroInventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'XileRetro';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Inventory';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Inventory';

    protected static ?string $pluralModelLabel = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->label('ID')
                    ->disabled(),
                TextInput::make('char_id')
                    ->label('Character ID')
                    ->numeric(),
                TextInput::make('nameid')
                    ->label('Item ID')
                    ->numeric(),
                TextInput::make('amount')
                    ->numeric(),
                TextInput::make('refine')
                    ->numeric(),
                TextInput::make('card0')
                    ->numeric(),
                TextInput::make('card1')
                    ->numeric(),
                TextInput::make('card2')
                    ->numeric(),
                TextInput::make('card3')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('char_id')
                    ->label('Character ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('nameid')
                    ->label('Item ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->sortable(),
                TextColumn::make('refine')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 10 => 'danger',
                        $state >= 7 => 'warning',
                        $state > 0 => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('card0')
                    ->label('Card 1')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('card1')
                    ->label('Card 2')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('card2')
                    ->label('Card 3')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('card3')
                    ->label('Card 4')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('refined')
                    ->label('Refined Items')
                    ->query(fn (Builder $query): Builder => $query->where('refine', '>', 0)),
                Filter::make('has_cards')
                    ->label('Has Cards')
                    ->query(fn (Builder $query): Builder => $query->where(function ($q) {
                        $q->where('card0', '>', 0)
                            ->orWhere('card1', '>', 0)
                            ->orWhere('card2', '>', 0)
                            ->orWhere('card3', '>', 0);
                    })),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListXileRetroInventories::route('/'),
            'create' => CreateXileRetroInventory::route('/create'),
            'edit' => EditXileRetroInventory::route('/{record}/edit'),
        ];
    }
}
