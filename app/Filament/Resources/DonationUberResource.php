<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationUberResource\Pages;
use App\Filament\Resources\DonationUberResource\RelationManagers;
use App\Filament\Resources\LoginResource\RelationManagers\LoginRelationManager;
use App\Ragnarok\DonationUber;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationUberResource extends Resource
{
    protected static ?string $model = DonationUber::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Uber System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_id'),
                Forms\Components\TextInput::make('username')->alphaNum(),
                Forms\Components\TextInput::make('current_ubers')->numeric(),
                Forms\Components\TextInput::make('pending_ubers')->numeric(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_id'),
                Tables\Columns\TextColumn::make('username'),
                Tables\Columns\TextColumn::make('current_ubers'),
                Tables\Columns\TextColumn::make('pending_ubers'),
            ])
            ->filters([
                Filter::make('Has Pending Ubers')->query(fn (Builder $query): Builder => $query->where('pending_ubers', '>', 0)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            LoginRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonationUbers::route('/'),
            'create' => Pages\CreateDonationUber::route('/create'),
            'edit' => Pages\EditDonationUber::route('/{record}/edit'),
        ];
    }
}
