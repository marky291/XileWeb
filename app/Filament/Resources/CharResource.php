<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CharResource\Pages\CreateChar;
use App\Filament\Resources\CharResource\Pages\EditChar;
use App\Filament\Resources\CharResource\Pages\ListChars;
use App\Filament\Resources\LoginResource\RelationManagers\LoginRelationManager;
use App\Ragnarok\Char;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CharResource extends Resource
{
    protected static ?string $model = Char::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Player Management';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
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
            // 3rd Class (Transcendent)
            '4060' => 'Rune Knight', '4061' => 'Warlock', '4062' => 'Ranger', '4063' => 'Arch Bishop',
            '4064' => 'Mechanic', '4065' => 'Guillotine Cross', '4073' => 'Royal Guard', '4074' => 'Sorcerer',
            '4075' => 'Minstrel', '4076' => 'Wanderer', '4077' => 'Sura', '4078' => 'Genetic',
            '4079' => 'Shadow Chaser',
            // 4th Class
            '4252' => 'Dragon Knight', '4253' => 'Meister', '4254' => 'Shadow Cross', '4255' => 'Arch Mage',
            '4256' => 'Cardinal', '4257' => 'Windhawk', '4258' => 'Imperial Guard', '4259' => 'Biolo',
            '4260' => 'Abyss Chaser', '4261' => 'Elemental Master', '4262' => 'Inquisitor', '4263' => 'Troubadour',
            '4264' => 'Trouvere',
            // Expanded Class
            '23' => 'Super Novice', '24' => 'Gunslinger', '25' => 'Ninja', '4045' => 'Super Baby',
            '4046' => 'Taekwon', '4047' => 'Star Gladiator', '4049' => 'Soul Linker',
            '4190' => 'Ex. Super Novice', '4191' => 'Ex. Super Baby',
            '4211' => 'Kagerou', '4212' => 'Oboro', '4215' => 'Rebellion', '4218' => 'Summoner',
            '4239' => 'Star Emperor', '4240' => 'Soul Reaper',
            '4302' => 'Sky Emperor', '4303' => 'Soul Ascetic', '4304' => 'Shinkiro', '4305' => 'Shiranui',
            '4306' => 'Night Watch', '4307' => 'Hyper Novice', '4308' => 'Spirit Handler',
            // Baby Novice And Baby 1st Class
            '4023' => 'Baby Novice', '4024' => 'Baby Swordman', '4025' => 'Baby Magician', '4026' => 'Baby Archer',
            '4027' => 'Baby Acolyte', '4028' => 'Baby Merchant', '4029' => 'Baby Thief',
            // Baby 2nd Class
            '4030' => 'Baby Knight', '4031' => 'Baby Priest', '4032' => 'Baby Wizard', '4033' => 'Baby Blacksmith',
            '4034' => 'Baby Hunter', '4035' => 'Baby Assassin', '4037' => 'Baby Crusader', '4038' => 'Baby Monk',
            '4039' => 'Baby Sage', '4040' => 'Baby Rogue', '4041' => 'Baby Alchemist', '4042' => 'Baby Bard',
            '4043' => 'Baby Dancer',
            // Baby 3rd Class
            '4096' => 'Baby Rune Knight', '4097' => 'Baby Warlock', '4098' => 'Baby Ranger', '4099' => 'Baby Arch Bishop',
            '4100' => 'Baby Mechanic', '4101' => 'Baby Glt. Cross', '4102' => 'Baby Royal Guard', '4103' => 'Baby Sorcerer',
            '4104' => 'Baby Minstrel', '4105' => 'Baby Wanderer', '4106' => 'Baby Sura', '4107' => 'Baby Genetic',
            '4108' => 'Baby Shadow Chaser',
            // Expanded Baby Class
            '4220' => 'Baby Summoner', '4222' => 'Baby Ninja', '4223' => 'Baby Kagero', '4224' => 'Baby Oboro',
            '4225' => 'Baby Taekwon', '4226' => 'Baby Star Glad', '4227' => 'Baby Soul Linker', '4228' => 'Baby Gunslinger',
            '4229' => 'Baby Rebellion',
        ];

        return $form
            ->schema([
                TextInput::make('account_id')->readOnly(),
                TextInput::make('name'),
                Select::make('class')->options($classOptions),
                TextInput::make('last_login')->readOnly(),
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
            'index' => ListChars::route('/'),
            'create' => CreateChar::route('/create'),
            'edit' => EditChar::route('/{record}/edit'),
        ];
    }
}
