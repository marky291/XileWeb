<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationRewardTierResource\Pages\CreateDonationRewardTier;
use App\Filament\Resources\DonationRewardTierResource\Pages\EditDonationRewardTier;
use App\Filament\Resources\DonationRewardTierResource\Pages\ListDonationRewardTiers;
use App\Models\DonationRewardTier;
use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class DonationRewardTierResource extends Resource
{
    protected static ?string $model = DonationRewardTier::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $modelLabel = 'Reward Tier';

    protected static ?string $pluralModelLabel = 'Reward Tiers';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Donations';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Step 1: Basic Details')
                    ->description('Set the tier name and minimum donation amount required to trigger this tier')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tier Name')
                            ->placeholder('e.g., Bronze Supporter, Silver Donor, Gold VIP')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Display name shown to users when claiming rewards'),
                        TextInput::make('minimum_amount')
                            ->label('Minimum Donation Amount')
                            ->prefix('$')
                            ->numeric()
                            ->required()
                            ->minValue(0.01)
                            ->placeholder('10.00')
                            ->helperText('The minimum donation required to trigger this tier'),
                        Textarea::make('description')
                            ->label('Description (Optional)')
                            ->placeholder('Describe the rewards or thank the donor...')
                            ->rows(2)
                            ->maxLength(500)
                            ->helperText('Optional description shown to users')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Step 2: Trigger Settings')
                    ->description('Configure when this tier triggers and how often it can be claimed')
                    ->schema([
                        Select::make('trigger_type')
                            ->label('Trigger Type')
                            ->options([
                                DonationRewardTier::TRIGGER_PER_DONATION => 'Per Donation - Triggers for each qualifying donation',
                                DonationRewardTier::TRIGGER_LIFETIME => 'Lifetime Total - Triggers when cumulative donations reach threshold',
                            ])
                            ->required()
                            ->default(DonationRewardTier::TRIGGER_PER_DONATION)
                            ->helperText('Per Donation rewards repeat with each donation. Lifetime is a one-time milestone.'),
                        Select::make('claim_reset_period')
                            ->label('Claim Reset Period')
                            ->options([
                                '' => 'One-time only (never resets)',
                                DonationRewardTier::RESET_PER_DONATION => 'Per Donation (resets with each donation)',
                                DonationRewardTier::RESET_DAILY => 'Daily (resets at midnight UTC)',
                                DonationRewardTier::RESET_WEEKLY => 'Weekly (resets Monday)',
                                DonationRewardTier::RESET_MONTHLY => 'Monthly (resets 1st of month)',
                                DonationRewardTier::RESET_YEARLY => 'Yearly (resets January 1st)',
                            ])
                            ->default('')
                            ->helperText('How often this tier can be claimed again after the reset period'),
                        Toggle::make('is_cumulative')
                            ->label('Cumulative Rewards')
                            ->default(false)
                            ->helperText('If enabled, lower tiers will also be awarded when this tier triggers'),
                        TextInput::make('display_order')
                            ->label('Display Order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first in the list'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Step 3: Server & Status')
                    ->description('Select which server this tier is for and enable/disable the tier')
                    ->schema([
                        Select::make('server')
                            ->label('Server')
                            ->options([
                                'xilero' => 'XileRO',
                                'xileretro' => 'XileRetro',
                            ])
                            ->required()
                            ->default('xilero')
                            ->live()
                            ->afterStateHydrated(function (Set $set, ?DonationRewardTier $record) {
                                if ($record) {
                                    $set('server', $record->is_xileretro ? 'xileretro' : 'xilero');
                                }
                            })
                            ->helperText('Items will be filtered based on the selected server'),
                        Select::make('enabled')
                            ->label('Status')
                            ->options([
                                '1' => 'Enabled',
                                '0' => 'Disabled',
                            ])
                            ->default('1')
                            ->required()
                            ->helperText('Disabled tiers will not trigger for any donations'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),

                Section::make('Step 4: Reward Items')
                    ->description('Add the items that will be awarded when this tier is triggered')
                    ->schema([
                        Repeater::make('tierItems')
                            ->relationship()
                            ->label('')
                            ->schema([
                                Select::make('item_id')
                                    ->label('Item')
                                    ->options(function (Get $get): array {
                                        $server = $get('../../server');
                                        $isXileRetro = $server === 'xileretro';

                                        return Item::query()
                                            ->where('is_xileretro', $isXileRetro)
                                            ->orderBy('name')
                                            ->limit(500)
                                            ->get()
                                            ->mapWithKeys(fn (Item $item) => [
                                                $item->id => '<img src="'.e($item->icon()).'" class="inline-block w-5 h-5 mr-2" /> '.e($item->name).' <span class="text-gray-400">('.e($item->item_id).')</span>',
                                            ])
                                            ->toArray();
                                    })
                                    ->searchable()
                                    ->getSearchResultsUsing(function (string $search, Get $get): array {
                                        $server = $get('../../server');
                                        $isXileRetro = $server === 'xileretro';

                                        return Item::query()
                                            ->where('is_xileretro', $isXileRetro)
                                            ->where(function ($query) use ($search) {
                                                $query->where('name', 'like', "%{$search}%")
                                                    ->orWhere('item_id', 'like', "%{$search}%");
                                            })
                                            ->orderBy('name')
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(fn (Item $item) => [
                                                $item->id => '<img src="'.e($item->icon()).'" class="inline-block w-5 h-5 mr-2" /> '.e($item->name).' <span class="text-gray-400">('.e($item->item_id).')</span>',
                                            ])
                                            ->toArray();
                                    })
                                    ->getOptionLabelUsing(function ($value): ?string {
                                        $item = Item::find($value);
                                        if (! $item) {
                                            return null;
                                        }

                                        return '<img src="'.e($item->icon()).'" class="inline-block w-5 h-5 mr-2" /> '.e($item->name).' <span class="text-gray-400">('.e($item->item_id).')</span>';
                                    })
                                    ->allowHtml()
                                    ->required()
                                    ->placeholder('Search for an item...')
                                    ->columnSpan(2),
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                                TextInput::make('refine_level')
                                    ->label('Refine Level')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(20)
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(0)
                            ->addActionLabel('Add Reward Item')
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(function (array $state): ?string {
                                if (! $state['item_id']) {
                                    return 'New Item';
                                }
                                $item = Item::find($state['item_id']);
                                if (! $item) {
                                    return 'New Item';
                                }

                                return $item->name.' ('.$item->item_id.') x'.($state['quantity'] ?? 1);
                            }),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('minimum_amount')
                    ->label('Min Amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('tierItems_count')
                    ->label('Items')
                    ->counts('tierItems')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('trigger_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === DonationRewardTier::TRIGGER_PER_DONATION ? 'Per Donation' : 'Lifetime')
                    ->color(fn (string $state): string => $state === DonationRewardTier::TRIGGER_PER_DONATION ? 'info' : 'success'),
                TextColumn::make('claim_reset_period')
                    ->label('Reset')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? (DonationRewardTier::RESET_PERIODS[$state] ?? ucfirst($state)) : 'One-time')
                    ->color(fn (?string $state): string => $state ? 'warning' : 'gray'),
                IconColumn::make('is_cumulative')
                    ->label('Cumul.')
                    ->boolean()
                    ->alignCenter(),
                TextColumn::make('server')
                    ->label('Server')
                    ->badge()
                    ->getStateUsing(fn (DonationRewardTier $record): string => $record->is_xileretro ? 'XileRetro' : 'XileRO')
                    ->color(fn (DonationRewardTier $record): string => $record->is_xileretro ? 'info' : 'warning'),
                IconColumn::make('enabled')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('claims_count')
                    ->label('Claims')
                    ->counts('claims')
                    ->badge()
                    ->color('success'),
            ])
            ->defaultSort('display_order')
            ->filters([
                SelectFilter::make('trigger_type')
                    ->options([
                        DonationRewardTier::TRIGGER_PER_DONATION => 'Per Donation',
                        DonationRewardTier::TRIGGER_LIFETIME => 'Lifetime',
                    ]),
                TernaryFilter::make('enabled')
                    ->label('Status')
                    ->placeholder('All')
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled'),
                SelectFilter::make('server')
                    ->label('Server')
                    ->options([
                        'xilero' => 'XileRO',
                        'xileretro' => 'XileRetro',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'xilero') {
                            $query->where('is_xilero', true)->where('is_xileretro', false);
                        } elseif ($data['value'] === 'xileretro') {
                            $query->where('is_xileretro', true)->where('is_xilero', false);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('toggle')
                    ->icon(fn ($record) => $record->enabled ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->enabled ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['enabled' => ! $record->enabled])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('enable')
                        ->label('Enable Selected')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['enabled' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('disable')
                        ->label('Disable Selected')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn ($records) => $records->each->update(['enabled' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->reorderable('display_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDonationRewardTiers::route('/'),
            'create' => CreateDonationRewardTier::route('/create'),
            'edit' => EditDonationRewardTier::route('/{record}/edit'),
        ];
    }
}
