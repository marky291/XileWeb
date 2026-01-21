<?php

namespace App\Filament\Resources;

use App\Filament\Resources\XileRetroCharResource\Pages\CreateXileRetroChar;
use App\Filament\Resources\XileRetroCharResource\Pages\EditXileRetroChar;
use App\Filament\Resources\XileRetroCharResource\Pages\ListXileRetroChars;
use App\Filament\Resources\XileRetroCharResource\RelationManagers\InventoryRelationManager;
use App\XileRetro\XileRetro_Char as Char;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class XileRetroCharResource extends Resource
{
    protected static ?string $model = Char::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'XileRetro';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Characters';

    protected static ?string $modelLabel = 'Character';

    protected static ?string $pluralModelLabel = 'Characters';

    public static function form(Schema $schema): Schema
    {
        $classOptions = [
            // Novice / 1st Class
            '0' => 'Novice', '1' => 'Swordman', '2' => 'Magician', '3' => 'Archer',
            '4' => 'Acolyte', '5' => 'Merchant', '6' => 'Thief',
            // 2nd Class
            '7' => 'Knight', '8' => 'Priest', '9' => 'Wizard', '10' => 'Blacksmith',
            '11' => 'Hunter', '12' => 'Assassin', '14' => 'Crusader', '15' => 'Monk',
            '16' => 'Sage', '17' => 'Rogue', '18' => 'Alchemist', '19' => 'Bard',
            '20' => 'Dancer',
            // High Novice / High 1st Class
            '4001' => 'Novice High', '4002' => 'Swordman High', '4003' => 'Magician High', '4004' => 'Archer High',
            '4005' => 'Acolyte High', '4006' => 'Merchant High', '4007' => 'Thief High',
            // Transcendent 2nd Class
            '4008' => 'Lord Knight', '4009' => 'High Priest', '4010' => 'High Wizard', '4011' => 'Whitesmith',
            '4012' => 'Sniper', '4013' => 'Assassin Cross', '4015' => 'Paladin', '4016' => 'Champion',
            '4017' => 'Professor', '4018' => 'Stalker', '4019' => 'Creator', '4020' => 'Clown',
            '4021' => 'Gypsy',
            // 3rd Class (Regular)
            '4054' => 'Rune Knight', '4055' => 'Warlock', '4056' => 'Ranger', '4057' => 'Arch Bishop',
            '4058' => 'Mechanic', '4059' => 'Guillotine Cross', '4066' => 'Royal Guard', '4067' => 'Sorcerer',
            '4068' => 'Minstrel', '4069' => 'Wanderer', '4070' => 'Sura', '4071' => 'Genetic',
            '4072' => 'Shadow Chaser',
            // Expanded Class
            '23' => 'Super Novice', '24' => 'Gunslinger', '25' => 'Ninja',
            '4046' => 'Taekwon', '4047' => 'Star Gladiator', '4049' => 'Soul Linker',
        ];

        return $schema
            ->components([
                TextInput::make('account_id')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('name'),
                Select::make('class')->options($classOptions),
                TextInput::make('last_login')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_id'),
                TextColumn::make('login.userid')->searchable()->copyable(),
                TextColumn::make('name')->label('Character')->searchable()->copyable(),
                TextColumn::make('class'),
                TextColumn::make('last_login'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [
            InventoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListXileRetroChars::route('/'),
            'create' => CreateXileRetroChar::route('/create'),
            'edit' => EditXileRetroChar::route('/{record}/edit'),
        ];
    }
}
