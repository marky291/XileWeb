<?php

namespace App\Filament\Resources\MasterAccountResource\RelationManagers;

use App\Models\GameAccount;
use Exception;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GameAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'gameAccounts';

    protected static ?string $title = 'Game Accounts';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('server')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => GameAccount::SERVERS[$state] ?? $state)
                    ->color(fn (string $state) => $state === 'xilero' ? 'success' : 'warning'),
                TextColumn::make('userid')
                    ->label('Username')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('ragnarok_account_id')
                    ->label('Account ID')
                    ->sortable(),
                TextColumn::make('live_email')
                    ->label('Email (Live)')
                    ->getStateUsing(fn (GameAccount $record) => $this->getLiveLogin($record)?->email ?? 'N/A')
                    ->toggleable(),
                TextColumn::make('live_group_id')
                    ->label('Group (Live)')
                    ->badge()
                    ->getStateUsing(fn (GameAccount $record) => $this->getLiveLogin($record)?->group_id ?? 0)
                    ->color(fn ($state) => $state >= 99 ? 'danger' : ($state > 0 ? 'warning' : 'gray')),
                TextColumn::make('live_state')
                    ->label('State (Live)')
                    ->badge()
                    ->getStateUsing(fn (GameAccount $record) => $this->getLiveLogin($record)?->state ?? 0)
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        0 => 'Active',
                        5 => 'Banned',
                        default => "State {$state}",
                    })
                    ->color(fn (int $state) => match ($state) {
                        0 => 'success',
                        5 => 'danger',
                        default => 'warning',
                    }),
                TextColumn::make('live_lastlogin')
                    ->label('Last Login (Live)')
                    ->getStateUsing(fn (GameAccount $record) => $this->getLiveLogin($record)?->lastlogin ?? 'Never')
                    ->toggleable(),
                TextColumn::make('live_last_ip')
                    ->label('Last IP (Live)')
                    ->getStateUsing(fn (GameAccount $record) => $this->getLiveLogin($record)?->last_ip ?? 'N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('chars_count')
                    ->label('Characters')
                    ->getStateUsing(fn (GameAccount $record) => $this->getCharacterCount($record))
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('server')
                    ->options(GameAccount::SERVERS),
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->label('View Details')
                    ->modalHeading(fn (GameAccount $record) => "Game Account: {$record->userid}")
                    ->schema(fn (GameAccount $record) => $this->getGameAccountInfolist($record)),
            ])
            ->toolbarActions([]);
    }

    protected function getLiveLogin(GameAccount $record): mixed
    {
        static $cache = [];

        $key = $record->server.'-'.$record->ragnarok_account_id;

        if (! isset($cache[$key])) {
            try {
                $cache[$key] = $record->ragnarokLogin();
            } catch (Exception $e) {
                $cache[$key] = null;
            }
        }

        return $cache[$key];
    }

    protected function getCharacterCount(GameAccount $record): int
    {
        if (! $record->ragnarok_account_id) {
            return 0;
        }

        try {
            return $record->chars()->count();
        } catch (Exception $e) {
            return 0;
        }
    }

    protected function getGameAccountInfolist(GameAccount $record): array
    {
        $liveLogin = $this->getLiveLogin($record);
        $characters = $this->getCharactersForAccount($record);

        return [
            Section::make('Live Account Information')
                ->description('Data from game database')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('server')
                                ->badge()
                                ->formatStateUsing(fn () => GameAccount::SERVERS[$record->server] ?? $record->server)
                                ->color($record->server === 'xilero' ? 'success' : 'warning'),
                            TextEntry::make('userid')
                                ->label('Username')
                                ->copyable(),
                            TextEntry::make('ragnarok_account_id')
                                ->label('Game Account ID'),
                        ]),
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('live_email')
                                ->label('Email')
                                ->state($liveLogin?->email ?? 'N/A')
                                ->copyable(),
                            TextEntry::make('live_group_id')
                                ->label('Group ID')
                                ->state($liveLogin?->group_id ?? 0)
                                ->badge()
                                ->color(($liveLogin?->group_id ?? 0) >= 99 ? 'danger' : (($liveLogin?->group_id ?? 0) > 0 ? 'warning' : 'gray')),
                            TextEntry::make('live_state')
                                ->label('State')
                                ->state(match ($liveLogin?->state ?? 0) {
                                    0 => 'Active',
                                    5 => 'Banned',
                                    default => 'State '.($liveLogin?->state ?? 0),
                                })
                                ->badge()
                                ->color(match ($liveLogin?->state ?? 0) {
                                    0 => 'success',
                                    5 => 'danger',
                                    default => 'warning',
                                }),
                        ]),
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('live_lastlogin')
                                ->label('Last Login')
                                ->state($liveLogin?->lastlogin ?? 'Never'),
                            TextEntry::make('live_last_ip')
                                ->label('Last IP')
                                ->state($liveLogin?->last_ip ?? 'N/A')
                                ->copyable(),
                            TextEntry::make('live_logincount')
                                ->label('Login Count')
                                ->state($liveLogin?->logincount ?? 0),
                        ]),
                ])
                ->visible($liveLogin !== null),
            Section::make('Database Unavailable')
                ->description('Could not connect to game database')
                ->icon('heroicon-o-exclamation-triangle')
                ->iconColor('danger')
                ->visible($liveLogin === null),
            Section::make('Characters ('.count($characters).')')
                ->schema([
                    RepeatableEntry::make('characters')
                        ->state($characters)
                        ->schema([
                            Grid::make(4)
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Name')
                                        ->weight('bold'),
                                    TextEntry::make('class_name')
                                        ->label('Class'),
                                    TextEntry::make('base_level')
                                        ->label('Base Lv'),
                                    TextEntry::make('job_level')
                                        ->label('Job Lv'),
                                ]),
                            Grid::make(4)
                                ->schema([
                                    TextEntry::make('last_map')
                                        ->label('Location'),
                                    TextEntry::make('online')
                                        ->label('Status')
                                        ->badge()
                                        ->formatStateUsing(fn ($state) => $state ? 'Online' : 'Offline')
                                        ->color(fn ($state) => $state ? 'success' : 'gray'),
                                    TextEntry::make('zeny')
                                        ->label('Zeny')
                                        ->formatStateUsing(fn ($state) => number_format($state)),
                                    TextEntry::make('last_login')
                                        ->label('Last Login'),
                                ]),
                        ])
                        ->columns(1)
                        ->contained(false),
                ])
                ->collapsed(count($characters) > 3)
                ->visible(count($characters) > 0),
            Section::make('No Characters')
                ->description('This game account has no characters.')
                ->visible(count($characters) === 0 && $liveLogin !== null),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getCharactersForAccount(GameAccount $record): array
    {
        if (! $record->ragnarok_account_id) {
            return [];
        }

        try {
            $chars = $record->chars()->get();

            return $chars->map(fn ($char) => [
                'char_id' => $char->char_id,
                'name' => $char->name,
                'class' => $char->class,
                'class_name' => $this->getClassName($char->class),
                'base_level' => $char->base_level,
                'job_level' => $char->job_level,
                'last_map' => $char->last_map,
                'online' => $char->online,
                'zeny' => $char->zeny ?? 0,
                'last_login' => $char->last_login,
            ])->toArray();
        } catch (Exception $e) {
            return [];
        }
    }

    protected function getClassName(int $classId): string
    {
        $classes = [
            0 => 'Novice', 1 => 'Swordman', 2 => 'Magician', 3 => 'Archer',
            4 => 'Acolyte', 5 => 'Merchant', 6 => 'Thief',
            7 => 'Knight', 8 => 'Priest', 9 => 'Wizard', 10 => 'Blacksmith',
            11 => 'Hunter', 12 => 'Assassin', 14 => 'Crusader', 15 => 'Monk',
            16 => 'Sage', 17 => 'Rogue', 18 => 'Alchemist', 19 => 'Bard', 20 => 'Dancer',
            4001 => 'Novice High', 4002 => 'Swordman High', 4003 => 'Magician High',
            4004 => 'Archer High', 4005 => 'Acolyte High', 4006 => 'Merchant High', 4007 => 'Thief High',
            4008 => 'Lord Knight', 4009 => 'High Priest', 4010 => 'High Wizard', 4011 => 'Whitesmith',
            4012 => 'Sniper', 4013 => 'Assassin Cross', 4015 => 'Paladin', 4016 => 'Champion',
            4017 => 'Professor', 4018 => 'Stalker', 4019 => 'Creator', 4020 => 'Clown', 4021 => 'Gypsy',
            4054 => 'Rune Knight', 4055 => 'Warlock', 4056 => 'Ranger', 4057 => 'Arch Bishop',
            4058 => 'Mechanic', 4059 => 'Guillotine Cross', 4066 => 'Royal Guard', 4067 => 'Sorcerer',
            4068 => 'Minstrel', 4069 => 'Wanderer', 4070 => 'Sura', 4071 => 'Genetic', 4072 => 'Shadow Chaser',
            23 => 'Super Novice', 24 => 'Gunslinger', 25 => 'Ninja',
            4046 => 'Taekwon', 4047 => 'Star Gladiator', 4049 => 'Soul Linker',
        ];

        return $classes[$classId] ?? "Class {$classId}";
    }
}
