<?php

namespace App\Filament\Resources\DonationUberResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationUberRelationManager extends RelationManager
{
    protected static string $relationship = 'donationUber';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_id')
                    ->required()
                    ->readOnly()
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->account_id),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->readOnly()
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->userid),
                Forms\Components\TextInput::make('current_ubers')->numeric()
                    ->default(0)->readOnly(),
                Forms\Components\TextInput::make('pending_ubers')->numeric()
                    ->default(0),
                Section::make('Donation Actions')->schema([
                    Forms\Components\TextInput::make('ubers')->prefix('+')->label('Send Ubers')->hintAction(
                        Forms\Components\Actions\Action::make('Add to Pending Ubers')->action(function (Get $get, Set $set, $state) {
                            $set('pending_ubers', $state += $get('pending_ubers'));
                            $set('ubers', null);
                        })
                    )
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_id')
            ->columns([
                Tables\Columns\TextColumn::make('account_id'),
                Tables\Columns\TextColumn::make('username'),
                Tables\Columns\TextColumn::make('current_ubers'),
                Tables\Columns\TextColumn::make('pending_ubers'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
}
