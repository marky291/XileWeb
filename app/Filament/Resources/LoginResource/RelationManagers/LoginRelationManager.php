<?php

namespace App\Filament\Resources\LoginResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use App\Actions\MakeHashedLoginPassword;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoginRelationManager extends RelationManager
{
    protected static string $relationship = 'login';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                //Tables\Actions\CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ])
            ->emptyStateActions([
                //Tables\Actions\CreateAction::make(),
            ]);
    }
}
