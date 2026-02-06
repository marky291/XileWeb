<?php

namespace App\Filament\Pages;

use App\Filament\Resources\DonationLogResource;
use App\Jobs\SendDonationAppliedEmail;
use App\Models\DonationLog;
use App\Models\User;
use App\Services\DonationCalculator;
use App\Services\DonationRewardService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ApplyDonation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = 'Apply Donation';

    protected static string|\UnitEnum|null $navigationGroup = 'Donations';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Apply Donation';

    protected string $view = 'filament.pages.apply-donation';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Step 1: Select Master Account')
                    ->description('Search by email or name to find the player\'s master account')
                    ->schema([
                        Select::make('user_id')
                            ->label('Master Account')
                            ->placeholder('Search by email or name...')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => User::where('email', 'like', "%{$search}%")
                                ->orWhere('name', 'like', "%{$search}%")
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn ($user) => [$user->id => "{$user->email} ({$user->name})"])
                                ->toArray())
                            ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->email)
                            ->required()
                            ->live()
                            ->helperText('The master account that will receive the Ubers')
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if ($state) {
                                    $user = User::find($state);
                                    $set('current_balance', ($user?->uber_balance ?? 0).' Ubers');

                                    $total = DonationLog::where('user_id', $state)->sum('amount');
                                    $totalUbers = DonationLog::where('user_id', $state)->sum('total_ubers');
                                    $set('total_donated', '$'.number_format($total, 2).' donated, '.number_format($totalUbers).' Ubers received');
                                } else {
                                    $set('current_balance', '-');
                                    $set('total_donated', '-');
                                }
                            }),
                        TextInput::make('current_balance')
                            ->label('Current Uber Balance')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('-'),
                        TextInput::make('total_donated')
                            ->label('Lifetime Donations')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('-'),
                        ViewField::make('donation_history')
                            ->view('filament.pages.partials.donation-history-table')
                            ->viewData([
                                'getDonations' => fn () => $this->getSelectedUserDonations(),
                            ])
                            ->visible(fn (Get $get): bool => $get('user_id') !== null)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Section::make('Step 2: Donation Details')
                    ->description('Enter the donation amount received and how it was paid')
                    ->schema([
                        Select::make('donation_tier')
                            ->label('Donation Amount Received')
                            ->helperText('Select the tier or choose Custom Amount for other values')
                            ->options(function () {
                                $options = [];
                                foreach (config('donation.tiers') as $tier) {
                                    $options[$tier['amount']] = '$'.$tier['amount'].' = '.$tier['ubers'].' Ubers';
                                }
                                $options['custom'] = 'Custom Amount';

                                return $options;
                            })
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if ($state === 'custom' || $state === null) {
                                    $set('base_ubers', 0);
                                    $set('amount', null);
                                    $this->recalculateTotal($get, $set);

                                    return;
                                }

                                $tiers = collect(config('donation.tiers'));
                                $tier = $tiers->firstWhere('amount', (int) $state);
                                $set('base_ubers', $tier['ubers'] ?? 0);
                                $set('amount', $state);
                                $this->recalculateTotal($get, $set);
                            }),
                        TextInput::make('amount')
                            ->label('Custom Amount (USD)')
                            ->prefix('$')
                            ->numeric()
                            ->required()
                            ->minValue(fn (Get $get) => $get('donation_tier') === 'custom' ? config('donation.calculator.minimum_amount') : null)
                            ->live(onBlur: true)
                            ->disabled(fn (Get $get) => $get('donation_tier') !== 'custom')
                            ->dehydrated()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if ($get('donation_tier') === 'custom' && $state) {
                                    $amount = (float) $state;
                                    $calculatedUbers = DonationCalculator::calculate($amount);
                                    $set('base_ubers', $calculatedUbers);
                                    $this->recalculateTotal($get, $set);
                                }
                            })
                            ->helperText(fn (Get $get) => $get('donation_tier') === 'custom'
                                ? 'Enter the exact amount received. Rate increases with higher amounts.'
                                : 'Only available when Custom Amount is selected'),
                        Select::make('payment_method')
                            ->label('Payment Method Used')
                            ->helperText('Crypto payments receive a 10% bonus')
                            ->options(function () {
                                $options = [];
                                foreach (config('donation.payment_methods') as $key => $method) {
                                    $label = $method['name'];
                                    if ($method['bonus'] > 0) {
                                        $label .= ' (+'.($method['bonus']).'% bonus)';
                                    }
                                    $options[$key] = $label;
                                }

                                return $options;
                            })
                            ->required()
                            ->default('paypal')
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculateTotal($get, $set)),
                        TextInput::make('base_ubers')
                            ->label('Base Ubers from Tier')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->live()
                            ->disabled(fn (Get $get) => $get('donation_tier') === 'custom')
                            ->dehydrated()
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculateTotal($get, $set))
                            ->helperText(fn (Get $get) => $get('donation_tier') === 'custom'
                                ? 'Auto-calculated from custom amount'
                                : 'Based on selected donation tier'),
                        TextInput::make('extra_ubers')
                            ->label('Bonus Ubers (Optional)')
                            ->numeric()
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalculateTotal($get, $set))
                            ->helperText('Extra Ubers to add as a gift or promotion'),
                        Textarea::make('notes')
                            ->label('Admin Notes')
                            ->placeholder('Transaction ID, PayPal email, Discord ticket #, reason for bonus, etc.')
                            ->rows(2)
                            ->helperText('Internal reference - not shown to the user')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Step 3: Review & Confirm')
                    ->description('Review the donation details before applying')
                    ->schema([
                        TextInput::make('review_user')
                            ->label('Master Account')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('-'),
                        TextInput::make('review_amount')
                            ->label('Donation Amount')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('-'),
                        TextInput::make('review_payment')
                            ->label('Payment Method')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('-'),
                        TextInput::make('review_base')
                            ->label('Base Ubers from Tier')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('0'),
                        TextInput::make('review_bonus')
                            ->label('Additional Bonus')
                            ->disabled()
                            ->dehydrated(false)
                            ->default('0'),
                        TextInput::make('total_ubers')
                            ->label('TOTAL Ubers to Apply')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->disabled()
                            ->dehydrated()
                            ->extraAttributes(['class' => 'text-xl font-bold']),
                        ViewField::make('reward_preview')
                            ->view('filament.pages.partials.donation-reward-preview')
                            ->viewData([
                                'getApplicableTiers' => fn () => $this->getApplicableRewardTiers(),
                            ])
                            ->visible(fn (Get $get): bool => $get('user_id') !== null && $get('amount') !== null)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function getSelectedUserDonations()
    {
        $userId = $this->data['user_id'] ?? null;

        if (! $userId) {
            return collect();
        }

        return DonationLog::where('user_id', $userId)
            ->with('admin')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getApplicableRewardTiers()
    {
        $userId = $this->data['user_id'] ?? null;
        $amount = $this->data['amount'] ?? null;

        if (! $userId || ! $amount) {
            return collect();
        }

        $user = User::find($userId);

        if (! $user) {
            return collect();
        }

        $rewardService = app(DonationRewardService::class);

        return $rewardService->getApplicableTiersPreview((float) $amount, $user);
    }

    protected function recalculateTotal(Get $get, Set $set): void
    {
        $paymentMethod = $get('payment_method');
        $bonus = config("donation.payment_methods.{$paymentMethod}.bonus", 0);
        $baseUbers = (int) ($get('base_ubers') ?? 0);
        $bonusUbers = (int) floor($baseUbers * ($bonus / 100));
        $extraUbers = (int) ($get('extra_ubers') ?? 0);
        $set('total_ubers', $baseUbers + $bonusUbers + $extraUbers);

        // Update review fields
        $userId = $get('user_id');
        $set('review_user', $userId ? (User::find($userId)?->email ?? '-') : '-');
        $set('review_amount', $get('amount') ? '$'.$get('amount') : '-');
        $set('review_payment', $paymentMethod ? config("donation.payment_methods.{$paymentMethod}.name", '-') : '-');
        $set('review_base', (string) $baseUbers);
        $set('review_bonus', ($bonusUbers + $extraUbers).' (Payment bonus: '.$bonusUbers.', Extra: '.$extraUbers.')');
    }

    public function apply(): void
    {
        $data = $this->form->getState();

        $user = User::findOrFail($data['user_id']);
        $paymentMethod = $data['payment_method'];
        $bonus = config("donation.payment_methods.{$paymentMethod}.bonus", 0);
        $baseUbers = (int) $data['base_ubers'];
        $bonusUbers = (int) floor($baseUbers * ($bonus / 100));
        $extraUbers = (int) ($data['extra_ubers'] ?? 0);
        $totalUbers = $baseUbers + $bonusUbers + $extraUbers;

        // Create donation log
        $donationLog = DonationLog::create([
            'user_id' => $user->id,
            'admin_id' => auth()->id(),
            'amount' => $data['amount'],
            'payment_method' => $paymentMethod,
            'base_ubers' => $baseUbers,
            'bonus_ubers' => $bonusUbers + $extraUbers,
            'total_ubers' => $totalUbers,
            'notes' => $data['notes'] ?? null,
        ]);

        // Apply ubers to user
        $user->increment('uber_balance', $totalUbers);
        $newBalance = $user->fresh()->uber_balance;

        // Apply bonus reward items
        $rewardService = app(DonationRewardService::class);
        $rewardClaims = $rewardService->applyRewards($donationLog);
        $rewardCount = $rewardClaims->count();

        // Prepare bonus rewards data for email, grouped by server
        $bonusRewards = [
            'xilero' => [],
            'xileretro' => [],
        ];

        foreach ($rewardClaims as $claim) {
            $rewardData = [
                'item_name' => $claim->item->name,
                'item_id' => $claim->item->item_id,
                'quantity' => $claim->quantity,
                'refine_level' => $claim->refine_level,
                'icon_url' => $claim->item->icon(),
            ];

            if ($claim->is_xilero) {
                $bonusRewards['xilero'][] = $rewardData;
            }
            if ($claim->is_xileretro) {
                $bonusRewards['xileretro'][] = $rewardData;
            }
        }

        // Schedule thank you email (delayed 10 minutes to allow admin to revert if needed)
        SendDonationAppliedEmail::dispatch(
            donationLog: $donationLog,
            amount: (float) $data['amount'],
            totalUbers: $totalUbers,
            newBalance: $newBalance,
            paymentMethod: $paymentMethod,
            bonusRewards: $bonusRewards,
        )->delay(now()->addMinutes(10));

        $message = "Added {$totalUbers} Ubers to {$user->email}. New balance: {$newBalance} Ubers.";

        if ($rewardCount > 0) {
            $message .= " {$rewardCount} bonus reward item(s) added to pending claims.";
        }

        $message .= ' Thank you email will be sent in 10 minutes.';

        Notification::make()
            ->title('Donation Applied Successfully!')
            ->body($message)
            ->success()
            ->send();

        $this->redirect(DonationLogResource::getUrl('index'));
    }
}
