<?php

namespace App\Filament\Resources\DonationUberResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Forms\Components\Get;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DonationUberRelationManager extends RelationManager
{
    protected static string $relationship = 'donationUber';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('account_id')
                    ->required()
                    ->readOnly()
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->account_id),
                TextInput::make('username')
                    ->required()
                    ->readOnly()
                    ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->userid),
                TextInput::make('current_ubers')->numeric()
                    ->default(0)->readOnly(),
                TextInput::make('pending_ubers')->numeric()
                    ->default(0),
                Section::make('Donation Actions')->schema([
                    TextInput::make('ubers')->prefix('+')->label('Send Ubers')->hintAction(
                        Action::make('Add to Pending Ubers')->action(function (Get $get, Set $set, $state) {
                            $set('pending_ubers', $state += $get('pending_ubers'));
                            $set('ubers', null);
                        })
                    ),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('account_id')
            ->columns([
                TextColumn::make('account_id'),
                TextColumn::make('username'),
                TextColumn::make('current_ubers'),
                TextColumn::make('pending_ubers'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
