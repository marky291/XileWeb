<?php

namespace App\Filament\Resources\LoginResource\RelationManagers;

use App\Actions\MakeHashedLoginPassword;
use Filament\Actions\Action;
use Filament\Forms\Components\Get;
use Filament\Forms\Components\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoginRelationManager extends RelationManager
{
    protected static string $relationship = 'login';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('account_id')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                TextInput::make('userid')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                TextInput::make('group_id')
                    ->required()
                    ->numeric(),
                TextInput::make('email')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                TextInput::make('user_pass')->hintAction(
                    Action::make('Create Hash')->action(function (Get $get, Set $set, $state) {
                        $set('user_pass', MakeHashedLoginPassword::run($state));
                    })
                ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_id')
            ->columns([
                TextColumn::make('account_id'),
                TextColumn::make('userid'),
                TextColumn::make('email'),
                TextColumn::make('group_id'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //                Tables\Actions\BulkActionGroup::make([
                //                    Tables\Actions\DeleteBulkAction::make(),
                //                ]),
            ]);
    }
}
