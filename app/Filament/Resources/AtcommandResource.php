<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtcommandResource\Pages\CreateAtcommand;
use App\Filament\Resources\AtcommandResource\Pages\EditAtcommand;
use App\Filament\Resources\AtcommandResource\Pages\ListAtcommands;
use App\Models\Atcommand;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AtcommandResource extends Resource
{
    protected static ?string $model = Atcommand::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-command-line';

    protected static string|\UnitEnum|null $navigationGroup = 'XileRO';

    protected static ?int $navigationSort = 4;

    // Hidden until Atcommand model is created
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('atcommand_date', 'desc')
            ->columns([
                TextColumn::make('account_id'),
                //                Tables\Columns\TextColumn::make('char_id'),
                TextColumn::make('char_name')->searchable(),
                TextColumn::make('map')->sortable(),
                TextColumn::make('command'),
                TextColumn::make('atcommand_date')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAtcommands::route('/'),
            'create' => CreateAtcommand::route('/create'),
            'edit' => EditAtcommand::route('/{record}/edit'),
        ];
    }
}
