<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationUberResource\Pages\CreateDonationUber;
use App\Filament\Resources\DonationUberResource\Pages\EditDonationUber;
use App\Filament\Resources\DonationUberResource\Pages\ListDonationUbers;
use App\Filament\Resources\LoginResource\RelationManagers\LoginRelationManager;
use Filament\Actions\Action;
use Filament\Forms\Components\Get;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DonationUberResource extends Resource
{
    protected static ?string $model = DonationUber::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Uber System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('account_id'),
                TextInput::make('username')->alphaNum(),
                TextInput::make('current_ubers')->numeric(),
                TextInput::make('pending_ubers')->numeric(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_id'),
                TextColumn::make('username'),
                TextColumn::make('current_ubers'),
                TextColumn::make('pending_ubers'),
            ])
            ->filters([
                Filter::make('Has Pending Ubers')->query(fn (Builder $query): Builder => $query->where('pending_ubers', '>', 0)),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                //                Tables\Actions\BulkActionGroup::make([
                //                    Tables\Actions\DeleteBulkAction::make(),
                //                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LoginRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonationUbers::route('/'),
            'create' => CreateDonationUber::route('/create'),
            'edit' => EditDonationUber::route('/{record}/edit'),
        ];
    }
}
