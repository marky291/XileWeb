<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtcommandResource\Pages\CreateAtcommand;
use App\Filament\Resources\AtcommandResource\Pages\EditAtcommand;
use App\Filament\Resources\AtcommandResource\Pages\ListAtcommands;
use App\Models\Atcommand;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AtcommandResource extends Resource
{
    protected static ?string $model = Atcommand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Logs';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
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
