<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AtcommandResource\Pages;
use App\Filament\Resources\AtcommandResource\RelationManagers;
use App\Models\Atcommand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
            ->columns([
                Tables\Columns\TextColumn::make('account_id'),
//                Tables\Columns\TextColumn::make('char_id'),
                Tables\Columns\TextColumn::make('char_name')->searchable(),
                Tables\Columns\TextColumn::make('map')->sortable(),
                Tables\Columns\TextColumn::make('command'),
                Tables\Columns\TextColumn::make('atcommand_date')->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListAtcommands::route('/'),
            'create' => Pages\CreateAtcommand::route('/create'),
            'edit' => Pages\EditAtcommand::route('/{record}/edit'),
        ];
    }
}
