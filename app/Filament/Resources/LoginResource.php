<?php

namespace App\Filament\Resources;

use App\Actions\MakeHashedLoginPassword;
use App\Filament\Resources\CharResource\RelationManagers\CharRelationManager;
use App\Filament\Resources\DonationUberResource\RelationManagers\DonationUberRelationManager;
use App\Filament\Resources\LoginResource\Pages;
use App\Filament\Resources\LoginResource\RelationManagers;
use App\Ragnarok\Login;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
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

    protected static ?string $navigationGroup = 'Player Management';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_id')->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('userid')->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('email')->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('user_pass')->hintAction(
                    Forms\Components\Actions\Action::make('Create Hash')->action(function (Get $get, Set $set, $state) {
                        $set('user_pass', MakeHashedLoginPassword::run($state));
                    })
                ),
                Forms\Components\TextInput::make('group_id')->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_id'),
                Tables\Columns\TextColumn::make('userid')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('chars_count')->counts('chars'),
                Tables\Columns\TextColumn::make('group_id')->sortable(),
                Tables\Columns\TextColumn::make('lastlogin'),
            ])
            ->filters([
                Filter::make('Staff')->query(fn (Builder $query): Builder => $query->where('group_id', '>', 1)),
                Filter::make('Streamers')->query(fn (Builder $query): Builder => $query->where('group_id', '=', 1)),
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
            CharRelationManager::class,
            DonationUberRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogins::route('/'),
            'create' => Pages\CreateLogin::route('/create'),
            'edit' => Pages\EditLogin::route('/{record}/edit'),
        ];
    }
}
