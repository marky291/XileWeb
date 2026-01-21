<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MasterAccountResource\Pages\CreateMasterAccount;
use App\Filament\Resources\MasterAccountResource\Pages\EditMasterAccount;
use App\Filament\Resources\MasterAccountResource\Pages\ListMasterAccounts;
use App\Filament\Resources\MasterAccountResource\RelationManagers\GameAccountsRelationManager;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MasterAccountResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static string|\UnitEnum|null $navigationGroup = 'Accounts';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationLabel = 'Master Accounts';

    protected static ?string $modelLabel = 'Master Account';

    protected static ?string $pluralModelLabel = 'Master Accounts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->maxLength(255)
                            ->helperText('Leave blank to keep current password when editing'),
                    ]),

                Section::make('Account Settings')
                    ->schema([
                        TextInput::make('uber_balance')
                            ->label('Uber Balance')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Use the Apply Donation page to modify Uber balance'),
                        TextInput::make('max_game_accounts')
                            ->label('Max Game Accounts')
                            ->numeric()
                            ->default(6)
                            ->helperText('Maximum number of game accounts this user can create'),
                    ]),

                Section::make('Permissions')
                    ->schema([
                        Toggle::make('is_admin')
                            ->label('Administrator')
                            ->helperText('Grant access to the admin dashboard'),
                    ]),

                Section::make('Discord')
                    ->collapsed()
                    ->schema([
                        TextInput::make('discord_id')
                            ->label('Discord ID')
                            ->disabled(),
                        TextInput::make('discord_username')
                            ->label('Discord Username')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('uber_balance')
                    ->label('Ubers')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('game_accounts_count')
                    ->label('Game Accounts')
                    ->counts('gameAccounts')
                    ->sortable(),
                IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('discord_username')
                    ->label('Discord')
                    ->placeholder('Not linked')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_admin')
                    ->label('Administrators'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            GameAccountsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMasterAccounts::route('/'),
            'create' => CreateMasterAccount::route('/create'),
            'edit' => EditMasterAccount::route('/{record}/edit'),
        ];
    }
}
