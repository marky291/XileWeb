<?php

namespace App\Livewire\Auth;

use App\Actions\CreateGameAccount;
use App\Actions\ResetCharacterPosition;
use App\Actions\ResetGameAccountPassword;
use App\Actions\SyncGameAccountData;
use App\Models\DonationRewardClaim;
use App\Models\GameAccount;
use App\Models\SyncedCharacter;
use App\Notifications\GameAccountPasswordResetNotification;
use App\Services\DonationRewardService;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Exception;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public bool $showCreateForm = false;

    public string $gameServer = 'xilero';

    public string $gameUsername = '';

    public string $gamePassword = '';

    public string $gamePassword_confirmation = '';

    public ?int $selectedGameAccountId = null;

    public ?int $selectedCharacterId = null;

    public ?int $resettingPasswordFor = null;

    public string $newPassword = '';

    public string $newPassword_confirmation = '';

    // Donation reward claim properties
    public ?int $rewardGameAccountId = null;

    public ?int $claimingRewardId = null;

    public bool $showClaimConfirm = false;

    /**
     * Sanitize showCreateForm input to prevent array injection attacks.
     */
    public function updatingShowCreateForm(mixed &$value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    /**
     * Sanitize gameServer input to prevent array injection attacks.
     */
    public function updatingGameServer(mixed &$value): void
    {
        $value = is_string($value) ? $value : 'xilero';
    }

    /**
     * Sanitize gameUsername input to prevent array injection attacks.
     */
    public function updatingGameUsername(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize gamePassword input to prevent array injection attacks.
     */
    public function updatingGamePassword(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize gamePassword_confirmation input to prevent array injection attacks.
     */
    public function updatingGamePasswordConfirmation(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize newPassword input to prevent array injection attacks.
     */
    public function updatingNewPassword(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize newPassword_confirmation input to prevent array injection attacks.
     */
    public function updatingNewPasswordConfirmation(mixed &$value): void
    {
        $value = is_string($value) ? $value : '';
    }

    /**
     * Sanitize rewardGameAccountId input to prevent array injection attacks.
     */
    public function updatingRewardGameAccountId(mixed &$value): void
    {
        $value = is_numeric($value) ? (int) $value : null;
    }

    /**
     * Sanitize claimingRewardId input to prevent array injection attacks.
     */
    public function updatingClaimingRewardId(mixed &$value): void
    {
        $value = is_numeric($value) ? (int) $value : null;
    }

    /**
     * Sanitize showClaimConfirm input to prevent array injection attacks.
     */
    public function updatingShowClaimConfirm(mixed &$value): void
    {
        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
    }

    public function mount(): void
    {
        $firstAccount = auth()->user()->gameAccounts()->first();
        if ($firstAccount) {
            $this->rewardGameAccountId = $firstAccount->id;
        }
    }

    /**
     * Validate reward game account selection when updated.
     */
    public function updatedRewardGameAccountId(?int $value): void
    {
        if (! $value) {
            $this->rewardGameAccountId = null;

            return;
        }

        $account = auth()->user()->gameAccounts()->find($value);
        if (! $account) {
            $firstAccount = auth()->user()->gameAccounts()->first();
            $this->rewardGameAccountId = $firstAccount?->id;
        }

        $this->cancelRewardClaim();
    }

    public function rules(): array
    {
        $loginModel = $this->gameServer === 'xileretro' ? XileRetro_Login::class : XileRO_Login::class;

        return [
            'gameServer' => [
                'required',
                'string',
                'in:xilero,xileretro',
            ],
            'gameUsername' => [
                'required',
                'string',
                'alpha_num',
                'min:4',
                'max:23',
                "unique:game_accounts,userid,NULL,id,server,{$this->gameServer}",
                "unique:{$loginModel},userid",
            ],
            'gamePassword' => [
                'required',
                'string',
                'min:6',
                'max:31',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'gameUsername.unique' => 'This username is already taken on this server.',
            'gameUsername.alpha_num' => 'Username must contain only letters and numbers.',
            'gamePassword.confirmed' => 'The password confirmation does not match.',
        ];
    }

    public function createGameAccount(): void
    {
        if (! auth()->user()->canCreateGameAccount()) {
            session()->flash('error', 'You have reached the maximum number of game accounts.');

            return;
        }

        $this->validate();

        CreateGameAccount::run(auth()->user(), [
            'server' => $this->gameServer,
            'userid' => $this->gameUsername,
            'email' => auth()->user()->email,
            'password' => $this->gamePassword,
            'sex' => 'M',
        ]);

        $this->reset(['gameServer', 'gameUsername', 'gamePassword', 'gamePassword_confirmation']);
        $this->gameServer = 'xilero';
        $this->showCreateForm = false;

        // Sync the new account's data
        SyncGameAccountData::run(auth()->user());

        session()->flash('success', 'Game account created successfully!');
    }

    public const REFRESH_COOLDOWN_SECONDS = 60;

    public function refreshData(): void
    {
        $sessionKey = 'last_game_data_refresh';
        $lastRefresh = session($sessionKey);
        $now = time();

        // Handle old Carbon values in session
        if ($lastRefresh && ! is_int($lastRefresh)) {
            $lastRefresh = null;
            session()->forget($sessionKey);
        }

        if ($lastRefresh && ($now - $lastRefresh) < self::REFRESH_COOLDOWN_SECONDS) {
            $remaining = self::REFRESH_COOLDOWN_SECONDS - ($now - $lastRefresh);
            session()->flash('error', "Please wait {$remaining} seconds before refreshing again.");

            return;
        }

        session([$sessionKey => $now]);

        $count = SyncGameAccountData::run(auth()->user());
        session()->flash('success', "Synced {$count} characters from game server.");
    }

    public function selectGameAccount(?int $gameAccountId): void
    {
        $this->selectedGameAccountId = $gameAccountId;
        $this->selectedCharacterId = null;
    }

    public function selectCharacter(?int $charId): void
    {
        $this->selectedCharacterId = $this->selectedCharacterId === $charId ? null : $charId;
    }

    public function selectedGameAccount(): ?GameAccount
    {
        if (! $this->selectedGameAccountId) {
            return null;
        }

        return auth()->user()->gameAccounts()->find($this->selectedGameAccountId);
    }

    public function resetPosition(int $charId): void
    {
        // Find the synced character to get the game account
        $syncedChar = SyncedCharacter::where('char_id', $charId)
            ->whereHas('gameAccount', fn ($q) => $q->where('user_id', auth()->id()))
            ->first();

        if (! $syncedChar) {
            session()->flash('error', 'Character not found.');

            return;
        }

        if ($syncedChar->online) {
            session()->flash('error', 'Cannot reset position for an online character.');

            return;
        }

        // Get the actual character from game DB for the reset action
        $gameAccount = $syncedChar->gameAccount;
        $character = $gameAccount->chars()->find($charId);

        if (! $character) {
            session()->flash('error', 'Character not found in game database.');

            return;
        }

        ResetCharacterPosition::run($character);

        // Update local synced data
        $syncedChar->update(['last_map' => 'prontera']);

        session()->flash('success', "{$syncedChar->name}'s position has been reset.");
    }

    public function resetSecurity(int $gameAccountId): void
    {
        $gameAccount = auth()->user()->gameAccounts()->find($gameAccountId);

        if (! $gameAccount) {
            session()->flash('error', 'Game account not found.');

            return;
        }

        if (! $gameAccount->hasSecurityCode()) {
            session()->flash('error', 'No security code is set for this account.');

            return;
        }

        // Check if any characters are online
        if ($gameAccount->hasOnlineCharacters()) {
            session()->flash('error', 'Cannot reset security while logged in. Please log out of the game first.');

            return;
        }

        if ($gameAccount->resetSecurityCode()) {
            $gameAccount->update(['has_security_code' => false]);
            session()->flash('success', "@security has been reset for {$gameAccount->userid}. You can set a new one in-game.");
        } else {
            session()->flash('error', 'Failed to reset security code.');
        }
    }

    public function showPasswordResetForm(int $gameAccountId): void
    {
        $gameAccount = auth()->user()->gameAccounts()->find($gameAccountId);

        if (! $gameAccount) {
            session()->flash('error', 'Game account not found.');

            return;
        }

        // Check if any characters are online
        if ($gameAccount->hasOnlineCharacters()) {
            session()->flash('error', 'Cannot reset password while logged in. Please log out of the game first.');

            return;
        }

        $this->resettingPasswordFor = $gameAccountId;
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
    }

    public function cancelPasswordReset(): void
    {
        $this->resettingPasswordFor = null;
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
        $this->resetErrorBag(['newPassword', 'newPassword_confirmation']);
    }

    public function resetPassword(): void
    {
        if (! $this->resettingPasswordFor) {
            return;
        }

        $this->validate([
            'newPassword' => ['required', 'string', 'min:6', 'max:31', 'confirmed'],
        ], [
            'newPassword.confirmed' => 'The password confirmation does not match.',
        ]);

        $gameAccount = auth()->user()->gameAccounts()->find($this->resettingPasswordFor);

        if (! $gameAccount) {
            session()->flash('error', 'Game account not found.');
            $this->cancelPasswordReset();

            return;
        }

        // Check again if any characters are online
        if ($gameAccount->hasOnlineCharacters()) {
            session()->flash('error', 'Cannot reset password while logged in. Please log out of the game first.');
            $this->cancelPasswordReset();

            return;
        }

        ResetGameAccountPassword::run($gameAccount, $this->newPassword);

        auth()->user()->notify(new GameAccountPasswordResetNotification($gameAccount));

        $this->cancelPasswordReset();

        session()->flash('success', "Password has been reset for {$gameAccount->userid}.");
    }

    // ========== Donation Reward Claim Methods ==========

    /**
     * Get the selected game account for reward claims.
     */
    public function rewardGameAccount(): ?GameAccount
    {
        if (! $this->rewardGameAccountId) {
            return null;
        }

        return auth()->user()->gameAccounts()->find($this->rewardGameAccountId);
    }

    /**
     * Get pending rewards for the current user, filtered by selected account's server.
     *
     * @return EloquentCollection<int, DonationRewardClaim>
     */
    public function pendingRewards(): EloquentCollection
    {
        $user = auth()->user();
        $query = $user->donationRewardClaims()
            ->pending()
            ->notExpired()
            ->with(['tier', 'item']);

        $gameAccount = $this->rewardGameAccount();
        if ($gameAccount) {
            $isRetro = $gameAccount->server === GameAccount::SERVER_XILERETRO;
            $query->forServer($isRetro);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get claimed rewards history.
     *
     * @return EloquentCollection<int, DonationRewardClaim>
     */
    public function claimedRewards(): EloquentCollection
    {
        return auth()->user()->donationRewardClaims()
            ->claimed()
            ->with(['tier', 'item'])
            ->orderBy('claimed_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get the count of all pending rewards (regardless of server filter).
     */
    public function totalPendingRewardsCount(): int
    {
        return auth()->user()->donationRewardClaims()
            ->pending()
            ->notExpired()
            ->count();
    }

    /**
     * Get pending rewards grouped by server type.
     *
     * @return array{xilero: EloquentCollection<int, DonationRewardClaim>, xileretro: EloquentCollection<int, DonationRewardClaim>}
     */
    public function pendingRewardsByServer(): array
    {
        $allPending = auth()->user()->donationRewardClaims()
            ->pending()
            ->notExpired()
            ->with(['tier', 'item'])
            ->get();

        return [
            'xilero' => $allPending->filter(fn ($r) => $r->is_xilero),
            'xileretro' => $allPending->filter(fn ($r) => $r->is_xileretro),
        ];
    }

    /**
     * Get game account IDs that have pending rewards claimable on them.
     *
     * @return array<int, bool>
     */
    public function accountsWithPendingRewards(): array
    {
        $pendingByServer = $this->pendingRewardsByServer();
        $gameAccounts = auth()->user()->gameAccounts;
        $result = [];

        foreach ($gameAccounts as $account) {
            $isRetro = $account->server === GameAccount::SERVER_XILERETRO;
            $serverKey = $isRetro ? 'xileretro' : 'xilero';
            $result[$account->id] = $pendingByServer[$serverKey]->isNotEmpty();
        }

        return $result;
    }

    /**
     * Start the claim process for a reward.
     */
    public function startRewardClaim(int $rewardId, ?int $gameAccountId = null): void
    {
        if ($gameAccountId !== null) {
            $this->rewardGameAccountId = $gameAccountId;
        }
        $this->claimingRewardId = $rewardId;
        $this->showClaimConfirm = true;
    }

    /**
     * Cancel the claim process.
     */
    public function cancelRewardClaim(): void
    {
        $this->claimingRewardId = null;
        $this->showClaimConfirm = false;
    }

    /**
     * Get the reward being claimed.
     */
    public function claimingReward(): ?DonationRewardClaim
    {
        if (! $this->claimingRewardId) {
            return null;
        }

        return DonationRewardClaim::with(['tier', 'item'])->find($this->claimingRewardId);
    }

    /**
     * Process the reward claim.
     */
    public function claimReward(): void
    {
        $gameAccount = $this->rewardGameAccount();
        if (! $gameAccount) {
            session()->flash('error', 'Please select a game account to receive the reward.');

            return;
        }

        $reward = $this->claimingReward();
        if (! $reward) {
            session()->flash('error', 'Reward not found.');
            $this->cancelRewardClaim();

            return;
        }

        if ($reward->user_id !== auth()->id()) {
            session()->flash('error', 'This reward does not belong to you.');
            $this->cancelRewardClaim();

            return;
        }

        if (! $reward->canBeClaimedBy($gameAccount)) {
            session()->flash('error', 'This reward cannot be claimed on the selected account.');
            $this->cancelRewardClaim();

            return;
        }

        try {
            $rewardService = app(DonationRewardService::class);
            $rewardService->claimReward($reward, $gameAccount);

            $itemName = $reward->item->name;
            $quantity = $reward->quantity;
            $refine = $reward->refine_level > 0 ? "+{$reward->refine_level} " : '';

            session()->flash('success', "{$refine}{$itemName} x{$quantity} will be delivered to {$gameAccount->userid} on next login.");

            $this->cancelRewardClaim();

        } catch (Exception $e) {
            session()->flash('error', 'Failed to claim reward: '.$e->getMessage());
        }
    }

    public function render()
    {
        $gameAccounts = auth()->user()->gameAccounts()
            ->with('syncedCharacters')
            ->get();

        $pendingByServer = $this->pendingRewardsByServer();

        // Calculate stats
        $totalCharacters = $gameAccounts->sum(fn ($a) => $a->syncedCharacters->count());
        $totalOnline = $gameAccounts->sum(fn ($a) => $a->syncedCharacters->where('online', true)->count());

        return view('livewire.auth.dashboard', [
            'user' => auth()->user(),
            'gameAccounts' => $gameAccounts,
            'canCreateMore' => auth()->user()->canCreateGameAccount(),
            'pendingRewards' => $this->pendingRewards(),
            'claimedRewards' => $this->claimedRewards(),
            'totalPendingRewardsCount' => $this->totalPendingRewardsCount(),
            'rewardGameAccount' => $this->rewardGameAccount(),
            'claimingReward' => $this->claimingReward(),
            'pendingRewardsByServer' => $pendingByServer,
            'accountsWithPendingRewards' => $this->accountsWithPendingRewards(),
            'totalCharacters' => $totalCharacters,
            'totalOnline' => $totalOnline,
        ]);
    }
}
