<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use App\Filament\Resources\LoginResource\Pages\ListLogins;
use App\Filament\Resources\LoginResource\Pages\CreateLogin;
use App\Filament\Resources\LoginResource\Pages\EditLogin;
use App\Actions\MakeHashedLoginPassword;
use App\Filament\Resources\CharResource\RelationManagers\CharRelationManager;
use App\Filament\Resources\DonationUberResource\RelationManagers\DonationUberRelationManager;
use App\Filament\Resources\LoginResource\Pages;
use App\Filament\Resources\LoginResource\RelationManagers;
use App\Ragnarok\Login;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoginResource extends Resource
{
    protected static ?string $model = Login::class;

    protected static ?string $recordTitleAttribute = 'userid';

    protected static string | \UnitEnum | null $navigationGroup = 'Player Management';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('account_id')->unique(ignoreRecord: true)->readOnly(),
                TextInput::make('userid')->unique(ignoreRecord: true),
                TextInput::make('email')->unique(ignoreRecord: true),
                TextInput::make('user_pass'),
                TextInput::make('group_id')->numeric(),
                TextInput::make('plain')->label('Plain Password')->hintAction(
                    Action::make('Create Hash')->action(function (Get $get, Set $set, $state) {
                        $set('user_pass', MakeHashedLoginPassword::run($state));
                    }),
                ),
                TextInput::make('last_ip')->readOnly(),
                DatePicker::make('lastlogin')->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_id'),
                TextColumn::make('userid')->searchable()->copyable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('chars_count')->counts('chars'),
                TextColumn::make('group_id')->sortable(),
                TextColumn::make('last_ip')->searchable()->copyable(),
                TextColumn::make('lastlogin'),
            ])
            ->filters([
                Filter::make('Staff')->query(fn (Builder $query): Builder => $query->where('group_id', '>', 1)),
                Filter::make('Streamers')->query(fn (Builder $query): Builder => $query->where('group_id', '=', 1)),
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
            CharRelationManager::class,
            DonationUberRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLogins::route('/'),
            'create' => CreateLogin::route('/create'),
            'edit' => EditLogin::route('/{record}/edit'),
        ];
    }
}
