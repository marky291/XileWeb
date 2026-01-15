<?php

namespace App\Filament\Resources;

use App\Actions\MakeHashedLoginPassword;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = Login::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Accounts';

    protected static ?string $modelLabel = 'Account';

    protected static ?string $pluralModelLabel = 'Accounts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('userid')
                    ->label('Username')
                    ->required()
                    ->maxLength(23),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(39),
                Select::make('sex')
                    ->options([
                        'M' => 'Male',
                        'F' => 'Female',
                    ])
                    ->default('M'),
                TextInput::make('group_id')
                    ->label('Group ID')
                    ->required()
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified'),
                TextInput::make('plain_password')
                    ->label('New Password')
                    ->password()
                    ->dehydrated(false)
                    ->hintAction(
                        Action::make('Hash Password')
                            ->action(function (Set $set, $state) {
                                if ($state) {
                                    $set('user_pass', MakeHashedLoginPassword::run($state));
                                }
                            }),
                    )
                    ->helperText('Enter a new password and click "Hash Password" to update'),
                TextInput::make('user_pass')
                    ->label('Password Hash')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('userid')
                    ->label('Username')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('sex')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'M' => 'info',
                        'F' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('group_id')
                    ->label('Group')
                    ->sortable(),
                TextColumn::make('lastlogin')
                    ->label('Last Login')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('logincount')
                    ->label('Logins')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
