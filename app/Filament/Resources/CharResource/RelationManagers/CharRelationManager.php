<?php

namespace App\Filament\Resources\CharResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CharRelationManager extends RelationManager
{
    protected static string $relationship = 'chars';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_id')
            ->columns([
                Tables\Columns\TextColumn::make('account_id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('last_login'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ])
            ->emptyStateActions([
                //Tables\Actions\CreateAction::make(),
            ]);
    }
}
