<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiTokenResource\Pages\CreateApiToken;
use App\Filament\Resources\ApiTokenResource\Pages\ListApiTokens;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'API Token';

    protected static ?string $pluralModelLabel = 'API Tokens';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Token Details')
                    ->description('Configure the API token settings')
                    ->icon('heroicon-o-key')
                    ->schema([
                        Select::make('tokenable_id')
                            ->label('User')
                            ->options(User::pluck('email', 'id'))
                            ->searchable()
                            ->required()
                            ->helperText('Select the user who owns this token'),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('A descriptive name for this token'),

                        CheckboxList::make('abilities')
                            ->options([
                                'read' => 'Read - Access data via API',
                                'write' => 'Write - Create and modify data via API',
                            ])
                            ->default(['read'])
                            ->helperText('Permissions this token grants'),

                        DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->nullable()
                            ->helperText('Leave empty for no expiration'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),

                TextColumn::make('tokenable.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('abilities')
                    ->badge()
                    ->separator(','),

                TextColumn::make('last_used_at')
                    ->label('Last Used')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Never')
                    ->sortable(),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('M j, Y g:i A')
                    ->placeholder('Never')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                DeleteAction::make(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiTokens::route('/'),
            'create' => CreateApiToken::route('/create'),
        ];
    }
}
