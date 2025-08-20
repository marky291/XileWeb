<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use App\Filament\Resources\DonationUberResource\Pages\ListDonationUbers;
use App\Filament\Resources\DonationUberResource\Pages\CreateDonationUber;
use App\Filament\Resources\DonationUberResource\Pages\EditDonationUber;
use App\Filament\Resources\DonationUberResource\Pages;
use App\Filament\Resources\DonationUberResource\RelationManagers;
use App\Filament\Resources\LoginResource\RelationManagers\LoginRelationManager;
use App\Ragnarok\DonationUber;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationUberResource extends Resource
{
    protected static ?string $model = DonationUber::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string | \UnitEnum | null $navigationGroup = 'Uber System';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    )
                ])
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
            ->recordActions([
                EditAction::make(),
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

    public static function getRelations(): array
    {
        return [
            LoginRelationManager::class
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
