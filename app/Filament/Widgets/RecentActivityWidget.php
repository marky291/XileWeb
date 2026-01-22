<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Master Account Registrations';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('registration_ip')
                    ->label('Reg IP')
                    ->searchable(),
                TextColumn::make('last_login_ip')
                    ->label('Last IP')
                    ->searchable(),
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->since(),
                TextColumn::make('uber_balance')
                    ->label('Ubers')
                    ->badge()
                    ->color('warning'),
                TextColumn::make('game_accounts_count')
                    ->counts('gameAccounts')
                    ->label('Game Accounts'),
                IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
