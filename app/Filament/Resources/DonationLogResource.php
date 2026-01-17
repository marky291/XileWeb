<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationLogResource\Pages;
use App\Models\DonationLog;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DonationLogResource extends Resource
{
    protected static ?string $model = DonationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Donations';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Donation History';

    protected static ?string $modelLabel = 'Donation';

    protected static ?string $pluralModelLabel = 'Donations';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Donation Details')
                    ->schema([
                        TextEntry::make('user.email')
                            ->label('User'),
                        TextEntry::make('admin.email')
                            ->label('Processed By'),
                        TextEntry::make('amount')
                            ->money('USD'),
                        TextEntry::make('payment_method')
                            ->formatStateUsing(fn (DonationLog $record) => $record->paymentMethodName()),
                    ])
                    ->columns(2),
                Section::make('Ubers Applied')
                    ->schema([
                        TextEntry::make('base_ubers')
                            ->label('Base Ubers'),
                        TextEntry::make('bonus_ubers')
                            ->label('Bonus Ubers'),
                        TextEntry::make('total_ubers')
                            ->label('Total Ubers')
                            ->badge()
                            ->color('success'),
                    ])
                    ->columns(3),
                Section::make('Additional Information')
                    ->schema([
                        TextEntry::make('notes')
                            ->placeholder('No notes'),
                        TextEntry::make('created_at')
                            ->label('Date')
                            ->dateTime(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->formatStateUsing(fn (DonationLog $record) => $record->paymentMethodName())
                    ->badge()
                    ->color(fn (string $state) => $state === 'crypto' ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('total_ubers')
                    ->label('Ubers')
                    ->sortable()
                    ->badge()
                    ->color(fn (DonationLog $record) => $record->isReverted() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('reverted_at')
                    ->label('Status')
                    ->formatStateUsing(fn (DonationLog $record) => $record->isReverted() ? 'Reverted' : 'Active')
                    ->badge()
                    ->color(fn (DonationLog $record) => $record->isReverted() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('admin.email')
                    ->label('Processed By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options(function () {
                        $options = [];
                        foreach (config('donation.payment_methods') as $key => $method) {
                            $options[$key] = $method['name'];
                        }

                        return $options;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('revert')
                    ->label('Revert')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->hidden(fn (DonationLog $record) => $record->isReverted())
                    ->requiresConfirmation()
                    ->modalHeading('Revert Donation')
                    ->modalDescription(function (DonationLog $record) {
                        $user = $record->user;
                        $currentBalance = $user->uber_balance;
                        $donationAmount = $record->total_ubers;
                        $newBalance = $currentBalance - $donationAmount;

                        if ($newBalance >= 0) {
                            return "This will remove {$donationAmount} Ubers from {$user->email}'s account (current balance: {$currentBalance} Ubers, new balance: {$newBalance} Ubers).";
                        }

                        return "Warning: {$user->email} has {$currentBalance} Ubers but the donation was for {$donationAmount} Ubers. Their balance will become {$newBalance} Ubers (negative). Future donations will count towards paying off this debt.";
                    })
                    ->modalSubmitActionLabel('Yes, Revert Donation')
                    ->action(function (DonationLog $record) {
                        $user = $record->user;
                        $donationAmount = $record->total_ubers;

                        // Remove full donation amount (can go negative)
                        $user->decrement('uber_balance', $donationAmount);
                        $newBalance = $user->fresh()->uber_balance;

                        // Mark donation as reverted (don't delete)
                        $record->update([
                            'reverted_at' => now(),
                            'reverted_by' => auth()->id(),
                            'ubers_recovered' => $donationAmount,
                        ]);

                        // Build notification message
                        if ($newBalance >= 0) {
                            Notification::make()
                                ->title('Donation Reverted')
                                ->body("Removed {$donationAmount} Ubers from {$user->email}. New balance: {$newBalance} Ubers.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Donation Reverted - Negative Balance')
                                ->body("Removed {$donationAmount} Ubers from {$user->email}. New balance: {$newBalance} Ubers. Future donations will count towards this debt.")
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonationLogs::route('/'),
            'view' => Pages\ViewDonationLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
