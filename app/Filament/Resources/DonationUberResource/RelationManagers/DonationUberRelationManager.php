<?php

namespace App\Filament\Resources\DonationUberResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationUberRelationManager extends RelationManager
{
    protected static string $relationship = 'donationUber';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')
                    ->required()
                    ->readOnly()
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->account_id),
                TextInput::make('username')
                    ->required()
                    ->readOnly()
                    ->default(fn(RelationManager $livewire) => $livewire->ownerRecord->userid),
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
                    )
                ])
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
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
