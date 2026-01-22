<?php

namespace App\Filament\Pages;

use App\Actions\MakeHashedLoginPassword;
use App\Actions\TransferLegacyUberBalance;
use App\Models\GameAccount;
use App\Models\User;
use App\XileRetro\XileRetro_Char;
use App\XileRetro\XileRetro_DonationUbers;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Char;
use App\XileRO\XileRO_Login;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlayerSupport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.player-support';

    public ?string $search = '';

    public ?string $searchType = 'email';

    public ?array $results = [];

    public ?array $selectedPlayer = null;

    // For linking legacy accounts (game account → master account)
    public ?int $linkToMasterAccountId = null;

    public ?string $masterAccountSearch = '';

    /** @var array<int, array{id: int, name: string, email: string}> */
    public array $masterAccountSearchResults = [];

    // For linking unclaimed game accounts to master account
    public ?string $unclaimedGameAccountSearch = '';

    /** @var array<int, array<string, mixed>> */
    public array $unclaimedGameAccountResults = [];

    public ?int $selectedUnclaimedGameAccountId = null;

    public ?string $selectedUnclaimedServer = null;

    // For transferring linked game accounts from master view
    public ?int $transferringGameAccountId = null;

    public ?string $transferTargetSearch = '';

    /** @var array<int, array{id: int, name: string, email: string}> */
    public array $transferTargetSearchResults = [];

    public ?int $transferTargetMasterAccountId = null;

    // For password reset
    public ?string $newPassword = '';

    // For game account password reset
    public ?string $newGamePassword = '';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('searchType')
                    ->label('Search By')
                    ->options([
                        'email' => 'Email',
                        'username' => 'Game Username',
                        'character' => 'Character Name',
                        'master_email' => 'Master Account Email',
                    ])
                    ->default('email')
                    ->live(),
                TextInput::make('search')
                    ->label('Search Term')
                    ->placeholder('Enter search term...')
                    ->minLength(2),
            ])
            ->columns(2);
    }

    public function searchPlayers(): void
    {
        if (strlen($this->search) < 2) {
            Notification::make()
                ->title('Search term too short')
                ->body('Please enter at least 2 characters')
                ->warning()
                ->send();

            return;
        }

        $this->results = [];
        $searchTerm = '%'.$this->search.'%';

        if ($this->searchType === 'master_email') {
            // Search master accounts
            $masters = User::where('email', 'like', $searchTerm)
                ->orWhere('name', 'like', $searchTerm)
                ->limit(20)
                ->get();

            foreach ($masters as $master) {
                $this->results[] = [
                    'type' => 'master',
                    'id' => $master->id,
                    'email' => $master->email,
                    'name' => $master->name,
                    'uber_balance' => $master->uber_balance,
                    'is_admin' => $master->is_admin,
                    'game_accounts_count' => $master->gameAccounts()->count(),
                    'created_at' => $master->created_at?->format('M j, Y'),
                ];
            }
        } elseif ($this->searchType === 'email' || $this->searchType === 'username') {
            $field = $this->searchType === 'email' ? 'email' : 'userid';

            // Search XileRO
            $xileroLogins = XileRO_Login::where($field, 'like', $searchTerm)
                ->limit(10)
                ->get();

            foreach ($xileroLogins as $login) {
                $linkedAccount = $this->getLinkedMasterAccount('xilero', $login->account_id);
                $this->results[] = [
                    'type' => 'xilero_login',
                    'server' => 'XileRO',
                    'server_key' => 'xilero',
                    'account_id' => $login->account_id,
                    'userid' => $login->userid,
                    'email' => $login->email,
                    'group_id' => $login->group_id,
                    'last_ip' => $login->last_ip,
                    'lastlogin' => $login->lastlogin,
                    'chars_count' => $login->chars()->count(),
                    'linked_master_id' => $linkedAccount?->user_id,
                    'linked_master_name' => $linkedAccount ? User::find($linkedAccount->user_id)?->name : null,
                ];
            }

            // Search XileRetro
            $xileretroLogins = XileRetro_Login::where($field, 'like', $searchTerm)
                ->limit(10)
                ->get();

            foreach ($xileretroLogins as $login) {
                $linkedAccount = $this->getLinkedMasterAccount('xileretro', $login->account_id);
                $this->results[] = [
                    'type' => 'xileretro_login',
                    'server' => 'XileRetro',
                    'server_key' => 'xileretro',
                    'account_id' => $login->account_id,
                    'userid' => $login->userid,
                    'email' => $login->email,
                    'group_id' => $login->group_id,
                    'last_ip' => $login->last_ip,
                    'lastlogin' => $login->lastlogin,
                    'chars_count' => $login->chars()->count(),
                    'linked_master_id' => $linkedAccount?->user_id,
                    'linked_master_name' => $linkedAccount ? User::find($linkedAccount->user_id)?->name : null,
                ];
            }
        } elseif ($this->searchType === 'character') {
            // Search XileRO characters
            $xileroChars = XileRO_Char::where('name', 'like', $searchTerm)
                ->with('login')
                ->limit(10)
                ->get();

            foreach ($xileroChars as $char) {
                $this->results[] = [
                    'type' => 'xilero_char',
                    'server' => 'XileRO',
                    'char_id' => $char->char_id,
                    'name' => $char->name,
                    'class' => $char->class,
                    'base_level' => $char->base_level,
                    'account_id' => $char->account_id,
                    'userid' => $char->login?->userid,
                    'last_map' => $char->last_map,
                    'online' => $char->online,
                ];
            }

            // Search XileRetro characters
            $xileretroChars = XileRetro_Char::where('name', 'like', $searchTerm)
                ->with('login')
                ->limit(10)
                ->get();

            foreach ($xileretroChars as $char) {
                $this->results[] = [
                    'type' => 'xileretro_char',
                    'server' => 'XileRetro',
                    'char_id' => $char->char_id,
                    'name' => $char->name,
                    'class' => $char->class,
                    'base_level' => $char->base_level,
                    'account_id' => $char->account_id,
                    'userid' => $char->login?->userid,
                    'last_map' => $char->last_map,
                    'online' => $char->online,
                ];
            }
        }

        if (empty($this->results)) {
            Notification::make()
                ->title('No results found')
                ->body('No players match your search criteria')
                ->info()
                ->send();
        }
    }

    protected function getLinkedMasterAccount(string $server, int $accountId): ?GameAccount
    {
        return GameAccount::where('server', $server)
            ->where('ragnarok_account_id', $accountId)
            ->first();
    }

    public function selectPlayer(int $index): void
    {
        $player = $this->results[$index] ?? null;

        // Toggle selection - clicking the same player deselects
        if ($this->selectedPlayer === $player) {
            $this->selectedPlayer = null;
        } else {
            $this->selectedPlayer = $player;
        }

        $this->resetLinkingFields();
        $this->newPassword = '';
        $this->newGamePassword = '';
    }

    public function clearSelection(): void
    {
        $this->selectedPlayer = null;
        $this->resetLinkingFields();
        $this->newPassword = '';
        $this->newGamePassword = '';
    }

    protected function resetLinkingFields(): void
    {
        $this->linkToMasterAccountId = null;
        $this->masterAccountSearch = '';
        $this->masterAccountSearchResults = [];
        $this->resetUnclaimedGameAccountFields();
    }

    protected function resetUnclaimedGameAccountFields(): void
    {
        $this->unclaimedGameAccountSearch = '';
        $this->unclaimedGameAccountResults = [];
        $this->selectedUnclaimedGameAccountId = null;
        $this->selectedUnclaimedServer = null;
    }

    public function updatedMasterAccountSearch(): void
    {
        if (strlen($this->masterAccountSearch) < 2) {
            $this->masterAccountSearchResults = [];

            return;
        }

        $searchTerm = '%'.$this->masterAccountSearch.'%';

        $this->masterAccountSearchResults = User::where('email', 'like', $searchTerm)
            ->orWhere('name', 'like', $searchTerm)
            ->limit(10)
            ->get(['id', 'name', 'email'])
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->toArray();
    }

    public function selectMasterAccountForLinking(int $id, string $name): void
    {
        $this->linkToMasterAccountId = $id;
        $this->masterAccountSearch = $name;
        $this->masterAccountSearchResults = [];
    }

    public function clearMasterAccountSelection(): void
    {
        $this->resetLinkingFields();
    }

    /**
     * Get linked game accounts for the selected master account.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLinkedGameAccountsProperty(): array
    {
        if (! $this->selectedPlayer || $this->selectedPlayer['type'] !== 'master') {
            return [];
        }

        return GameAccount::where('user_id', $this->selectedPlayer['id'])
            ->get()
            ->map(fn (GameAccount $ga) => [
                'id' => $ga->id,
                'server' => $ga->server,
                'server_name' => $ga->serverName(),
                'userid' => $ga->userid,
                'email' => $ga->email,
                'ragnarok_account_id' => $ga->ragnarok_account_id,
                'group_id' => $ga->group_id,
            ])
            ->toArray();
    }

    public function updatedUnclaimedGameAccountSearch(): void
    {
        if (strlen($this->unclaimedGameAccountSearch) < 2) {
            $this->unclaimedGameAccountResults = [];

            return;
        }

        $searchTerm = '%'.$this->unclaimedGameAccountSearch.'%';
        $results = [];

        // Search unclaimed GameAccounts (user_id is null)
        $unclaimedAccounts = GameAccount::whereNull('user_id')
            ->where(function ($query) use ($searchTerm) {
                $query->where('userid', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm);
            })
            ->limit(10)
            ->get();

        foreach ($unclaimedAccounts as $account) {
            $results[] = [
                'id' => $account->id,
                'server' => $account->server,
                'server_name' => $account->serverName(),
                'userid' => $account->userid,
                'email' => $account->email,
                'ragnarok_account_id' => $account->ragnarok_account_id,
                'type' => 'unclaimed',
            ];
        }

        $this->unclaimedGameAccountResults = $results;
    }

    public function selectUnclaimedGameAccount(int $id, string $server, string $userid): void
    {
        $this->selectedUnclaimedGameAccountId = $id;
        $this->selectedUnclaimedServer = $server;
        $this->unclaimedGameAccountSearch = $userid;
        $this->unclaimedGameAccountResults = [];
    }

    public function clearUnclaimedGameAccountSelection(): void
    {
        $this->resetUnclaimedGameAccountFields();
    }

    public function linkUnclaimedToMaster(): void
    {
        if (! $this->selectedPlayer || $this->selectedPlayer['type'] !== 'master') {
            Notification::make()
                ->title('Invalid selection')
                ->body('Please select a master account first')
                ->danger()
                ->send();

            return;
        }

        if (! $this->selectedUnclaimedGameAccountId) {
            Notification::make()
                ->title('No game account selected')
                ->body('Please select an unclaimed game account to link')
                ->warning()
                ->send();

            return;
        }

        $masterAccount = User::find($this->selectedPlayer['id']);
        if (! $masterAccount) {
            Notification::make()
                ->title('Master account not found')
                ->danger()
                ->send();

            return;
        }

        // Check if master account can have more game accounts
        if (! $masterAccount->canCreateGameAccount()) {
            Notification::make()
                ->title('Limit reached')
                ->body("Master account has reached maximum game accounts ({$masterAccount->max_game_accounts})")
                ->danger()
                ->send();

            return;
        }

        $gameAccount = GameAccount::find($this->selectedUnclaimedGameAccountId);
        if (! $gameAccount) {
            Notification::make()
                ->title('Game account not found')
                ->danger()
                ->send();

            return;
        }

        if ($gameAccount->user_id !== null) {
            Notification::make()
                ->title('Already linked')
                ->body('This game account is already linked to a master account')
                ->warning()
                ->send();

            return;
        }

        // Link the game account
        $gameAccount->update([
            'user_id' => $masterAccount->id,
        ]);

        // Transfer legacy uber balance if any
        $transferredUbers = TransferLegacyUberBalance::run($gameAccount, $masterAccount);

        $message = "Game account {$gameAccount->userid} ({$gameAccount->serverName()}) linked to {$masterAccount->name}";
        if ($transferredUbers > 0) {
            $message .= ". Transferred {$transferredUbers} legacy ubers.";
        }

        Notification::make()
            ->title('Account linked')
            ->body($message)
            ->success()
            ->send();

        // Update the game accounts count in the selected player
        $this->selectedPlayer['game_accounts_count'] = $masterAccount->gameAccounts()->count();

        // Update results array
        foreach ($this->results as $index => $result) {
            if ($result['type'] === 'master' && $result['id'] === $masterAccount->id) {
                $this->results[$index]['game_accounts_count'] = $this->selectedPlayer['game_accounts_count'];
                break;
            }
        }

        $this->resetUnclaimedGameAccountFields();
    }

    public function unlinkGameAccount(int $gameAccountId): void
    {
        $gameAccount = GameAccount::find($gameAccountId);

        if (! $gameAccount) {
            Notification::make()
                ->title('Game account not found')
                ->danger()
                ->send();

            return;
        }

        $userid = $gameAccount->userid;
        $serverName = $gameAccount->serverName();

        // Unlink (set user_id to null)
        $gameAccount->update([
            'user_id' => null,
        ]);

        Notification::make()
            ->title('Account unlinked')
            ->body("Game account {$userid} ({$serverName}) has been unlinked")
            ->success()
            ->send();

        // Update the game accounts count in the selected player
        if ($this->selectedPlayer && $this->selectedPlayer['type'] === 'master') {
            $masterAccount = User::find($this->selectedPlayer['id']);
            if ($masterAccount) {
                $this->selectedPlayer['game_accounts_count'] = $masterAccount->gameAccounts()->count();

                // Update results array
                foreach ($this->results as $index => $result) {
                    if ($result['type'] === 'master' && $result['id'] === $masterAccount->id) {
                        $this->results[$index]['game_accounts_count'] = $this->selectedPlayer['game_accounts_count'];
                        break;
                    }
                }
            }
        }
    }

    public function startTransferGameAccount(int $gameAccountId): void
    {
        $this->transferringGameAccountId = $gameAccountId;
        $this->transferTargetSearch = '';
        $this->transferTargetSearchResults = [];
        $this->transferTargetMasterAccountId = null;
    }

    public function cancelTransferGameAccount(): void
    {
        $this->transferringGameAccountId = null;
        $this->transferTargetSearch = '';
        $this->transferTargetSearchResults = [];
        $this->transferTargetMasterAccountId = null;
    }

    public function updatedTransferTargetSearch(): void
    {
        if (strlen($this->transferTargetSearch) < 2) {
            $this->transferTargetSearchResults = [];

            return;
        }

        $searchTerm = '%'.$this->transferTargetSearch.'%';

        $this->transferTargetSearchResults = User::where('email', 'like', $searchTerm)
            ->orWhere('name', 'like', $searchTerm)
            ->limit(10)
            ->get(['id', 'name', 'email'])
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->toArray();
    }

    public function selectTransferTarget(int $id, string $name): void
    {
        $this->transferTargetMasterAccountId = $id;
        $this->transferTargetSearch = $name;
        $this->transferTargetSearchResults = [];
    }

    public function executeTransferGameAccount(): void
    {
        if (! $this->transferringGameAccountId) {
            Notification::make()
                ->title('No game account selected')
                ->body('Please select a game account to transfer')
                ->warning()
                ->send();

            return;
        }

        if (! $this->transferTargetMasterAccountId) {
            Notification::make()
                ->title('No target account selected')
                ->body('Please select a master account to transfer to')
                ->warning()
                ->send();

            return;
        }

        $gameAccount = GameAccount::find($this->transferringGameAccountId);
        if (! $gameAccount) {
            Notification::make()
                ->title('Game account not found')
                ->danger()
                ->send();
            $this->cancelTransferGameAccount();

            return;
        }

        $targetMaster = User::find($this->transferTargetMasterAccountId);
        if (! $targetMaster) {
            Notification::make()
                ->title('Target master account not found')
                ->danger()
                ->send();

            return;
        }

        // Cannot transfer to the same account
        if ($gameAccount->user_id === $targetMaster->id) {
            Notification::make()
                ->title('Same account')
                ->body('This game account is already linked to this master account')
                ->warning()
                ->send();

            return;
        }

        // Check if target master can accept more game accounts
        if (! $targetMaster->canCreateGameAccount()) {
            Notification::make()
                ->title('Limit reached')
                ->body("Target master account has reached maximum game accounts ({$targetMaster->max_game_accounts})")
                ->danger()
                ->send();

            return;
        }

        $sourceMaster = User::find($gameAccount->user_id);
        $sourceAccountName = $sourceMaster?->name ?? 'Unknown';

        // Transfer the game account
        $gameAccount->update([
            'user_id' => $targetMaster->id,
        ]);

        Notification::make()
            ->title('Account transferred')
            ->body("Game account {$gameAccount->userid} ({$gameAccount->serverName()}) transferred from {$sourceAccountName} to {$targetMaster->name}")
            ->success()
            ->send();

        // Update the game accounts count in the selected player
        if ($this->selectedPlayer && $this->selectedPlayer['type'] === 'master') {
            $currentMaster = User::find($this->selectedPlayer['id']);
            if ($currentMaster) {
                $this->selectedPlayer['game_accounts_count'] = $currentMaster->gameAccounts()->count();

                // Update results array
                foreach ($this->results as $index => $result) {
                    if ($result['type'] === 'master' && $result['id'] === $currentMaster->id) {
                        $this->results[$index]['game_accounts_count'] = $this->selectedPlayer['game_accounts_count'];
                        break;
                    }
                }
            }
        }

        $this->cancelTransferGameAccount();
    }

    public function resetCharacterPosition(string $server, int $charId): void
    {
        $resetMap = config('xilero.character.reset.position.map', 'prontera');
        $resetX = config('xilero.character.reset.position.x', 156);
        $resetY = config('xilero.character.reset.position.y', 153);

        if ($server === 'XileRO') {
            $char = XileRO_Char::find($charId);
        } else {
            $char = XileRetro_Char::find($charId);
        }

        if (! $char) {
            Notification::make()
                ->title('Character not found')
                ->danger()
                ->send();

            return;
        }

        $char->update([
            'last_map' => $resetMap,
            'last_x' => $resetX,
            'last_y' => $resetY,
            'save_map' => $resetMap,
            'save_x' => $resetX,
            'save_y' => $resetY,
        ]);

        Notification::make()
            ->title('Position reset')
            ->body("Character {$char->name} moved to {$resetMap}")
            ->success()
            ->send();
    }

    public function linkGameAccountToMaster(): void
    {
        if (! $this->selectedPlayer || ! str_contains($this->selectedPlayer['type'], 'login')) {
            Notification::make()
                ->title('Invalid selection')
                ->body('Please select a game account (login) to link')
                ->danger()
                ->send();

            return;
        }

        if (! $this->linkToMasterAccountId) {
            Notification::make()
                ->title('No master account selected')
                ->body('Please enter a master account ID to link to')
                ->warning()
                ->send();

            return;
        }

        $masterAccount = User::find($this->linkToMasterAccountId);
        if (! $masterAccount) {
            Notification::make()
                ->title('Master account not found')
                ->body("No master account found with ID {$this->linkToMasterAccountId}")
                ->danger()
                ->send();

            return;
        }

        // Check if already linked
        $existingLink = GameAccount::where('server', $this->selectedPlayer['server_key'])
            ->where('ragnarok_account_id', $this->selectedPlayer['account_id'])
            ->first();

        if ($existingLink && $existingLink->user_id !== null) {
            $existingMaster = User::find($existingLink->user_id);
            Notification::make()
                ->title('Already linked')
                ->body("This game account is already linked to master account: {$existingMaster?->name} ({$existingMaster?->email})")
                ->warning()
                ->send();

            return;
        }

        // Check if master account can have more game accounts
        if (! $masterAccount->canCreateGameAccount()) {
            Notification::make()
                ->title('Limit reached')
                ->body("Master account has reached maximum game accounts ({$masterAccount->max_game_accounts})")
                ->danger()
                ->send();

            return;
        }

        // Get the live login data
        $login = $this->selectedPlayer['server_key'] === 'xilero'
            ? XileRO_Login::find($this->selectedPlayer['account_id'])
            : XileRetro_Login::find($this->selectedPlayer['account_id']);

        if (! $login) {
            Notification::make()
                ->title('Game account not found')
                ->danger()
                ->send();

            return;
        }

        // Check for legacy ubers if XileRetro account
        $legacyUbers = 0;
        if ($this->selectedPlayer['server_key'] === 'xileretro') {
            $legacyUbers = XileRetro_DonationUbers::getTotalUbersForAccount($login->account_id);
        }

        // Create or update the link
        if ($existingLink) {
            // For XileRetro, re-fetch legacy ubers to ensure current balance
            if ($this->selectedPlayer['server_key'] === 'xileretro') {
                $legacyUbers = XileRetro_DonationUbers::getTotalUbersForAccount($login->account_id);
            }

            // Update existing unclaimed record
            $existingLink->update([
                'user_id' => $masterAccount->id,
                'userid' => $login->userid,
                'user_pass' => $login->user_pass,
                'email' => $login->email,
                'sex' => $login->sex ?? 'M',
                'group_id' => $login->group_id,
                'state' => $login->state,
                'legacy_uber_balance' => $legacyUbers,
            ]);
            $gameAccount = $existingLink;
        } else {
            // Create new record
            $gameAccount = GameAccount::create([
                'user_id' => $masterAccount->id,
                'server' => $this->selectedPlayer['server_key'],
                'ragnarok_account_id' => $login->account_id,
                'userid' => $login->userid,
                'user_pass' => $login->user_pass,
                'email' => $login->email,
                'sex' => $login->sex ?? 'M',
                'group_id' => $login->group_id,
                'state' => $login->state,
                'legacy_uber_balance' => $legacyUbers,
            ]);
        }

        // Transfer legacy uber balance if any
        $transferredUbers = TransferLegacyUberBalance::run($gameAccount, $masterAccount);

        $message = "Game account {$login->userid} ({$this->selectedPlayer['server']}) linked to master account {$masterAccount->name}";
        if ($transferredUbers > 0) {
            $message .= ". Transferred {$transferredUbers} legacy ubers.";
        }

        Notification::make()
            ->title('Account linked')
            ->body($message)
            ->success()
            ->send();

        // Update the selected player and results array
        $this->selectedPlayer['linked_master_id'] = $masterAccount->id;
        $this->selectedPlayer['linked_master_name'] = $masterAccount->name;

        // Find and update the result in the results array
        foreach ($this->results as $index => $result) {
            if ($result === $this->selectedPlayer || (
                isset($result['account_id']) &&
                $result['account_id'] === $this->selectedPlayer['account_id'] &&
                $result['server_key'] === $this->selectedPlayer['server_key']
            )) {
                $this->results[$index]['linked_master_id'] = $masterAccount->id;
                $this->results[$index]['linked_master_name'] = $masterAccount->name;
                break;
            }
        }

        $this->resetLinkingFields();
    }

    public function transferGameAccountToMaster(): void
    {
        if (! $this->selectedPlayer || ! str_contains($this->selectedPlayer['type'], 'login')) {
            Notification::make()
                ->title('Invalid selection')
                ->body('Please select a game account (login) to transfer')
                ->danger()
                ->send();

            return;
        }

        if (! $this->linkToMasterAccountId) {
            Notification::make()
                ->title('No master account selected')
                ->body('Please select a master account to transfer to')
                ->warning()
                ->send();

            return;
        }

        $targetMasterAccount = User::find($this->linkToMasterAccountId);
        if (! $targetMasterAccount) {
            Notification::make()
                ->title('Master account not found')
                ->body("No master account found with ID {$this->linkToMasterAccountId}")
                ->danger()
                ->send();

            return;
        }

        // Find the existing link
        $existingLink = GameAccount::where('server', $this->selectedPlayer['server_key'])
            ->where('ragnarok_account_id', $this->selectedPlayer['account_id'])
            ->first();

        if (! $existingLink || $existingLink->user_id === null) {
            Notification::make()
                ->title('Not linked')
                ->body('This game account is not linked to any master account. Use "Link" instead.')
                ->warning()
                ->send();

            return;
        }

        // Cannot transfer to the same account
        if ($existingLink->user_id === $targetMasterAccount->id) {
            Notification::make()
                ->title('Same account')
                ->body('This game account is already linked to this master account')
                ->warning()
                ->send();

            return;
        }

        $sourceMasterAccount = User::find($existingLink->user_id);

        // Check if target master account can have more game accounts
        if (! $targetMasterAccount->canCreateGameAccount()) {
            Notification::make()
                ->title('Limit reached')
                ->body("Target master account has reached maximum game accounts ({$targetMasterAccount->max_game_accounts})")
                ->danger()
                ->send();

            return;
        }

        $sourceAccountName = $sourceMasterAccount?->name ?? 'Unknown';

        // Transfer the game account
        $existingLink->update([
            'user_id' => $targetMasterAccount->id,
        ]);

        Notification::make()
            ->title('Account transferred')
            ->body("Game account {$existingLink->userid} ({$existingLink->serverName()}) transferred from {$sourceAccountName} to {$targetMasterAccount->name}")
            ->success()
            ->send();

        // Update the selected player and results array
        $this->selectedPlayer['linked_master_id'] = $targetMasterAccount->id;
        $this->selectedPlayer['linked_master_name'] = $targetMasterAccount->name;

        // Find and update the result in the results array
        foreach ($this->results as $index => $result) {
            if (isset($result['account_id']) &&
                $result['account_id'] === $this->selectedPlayer['account_id'] &&
                $result['server_key'] === $this->selectedPlayer['server_key']
            ) {
                $this->results[$index]['linked_master_id'] = $targetMasterAccount->id;
                $this->results[$index]['linked_master_name'] = $targetMasterAccount->name;
                break;
            }
        }

        $this->resetLinkingFields();
    }

    public function resetMasterPassword(): void
    {
        if (! $this->selectedPlayer || $this->selectedPlayer['type'] !== 'master') {
            Notification::make()
                ->title('Invalid selection')
                ->body('Please select a master account to reset password')
                ->danger()
                ->send();

            return;
        }

        $user = User::find($this->selectedPlayer['id']);
        if (! $user) {
            Notification::make()
                ->title('User not found')
                ->danger()
                ->send();

            return;
        }

        // Generate a random password if not provided
        $password = $this->newPassword ?: Str::random(12);

        $user->update([
            'password' => Hash::make($password),
        ]);

        Notification::make()
            ->title('Password reset')
            ->body("New password for {$user->email}: {$password}")
            ->success()
            ->persistent()
            ->send();

        $this->newPassword = '';
    }

    public function resetGameAccountPassword(): void
    {
        if (! $this->selectedPlayer || ! str_contains($this->selectedPlayer['type'], 'login')) {
            Notification::make()
                ->title('Invalid selection')
                ->body('Please select a game account to reset password')
                ->danger()
                ->send();

            return;
        }

        // Generate a random password if not provided
        $password = $this->newGamePassword ?: Str::random(12);

        // Validate password length for game accounts
        if (strlen($password) < 6 || strlen($password) > 31) {
            Notification::make()
                ->title('Invalid password')
                ->body('Password must be between 6 and 31 characters')
                ->warning()
                ->send();

            return;
        }

        // Get the login model
        $serverKey = $this->selectedPlayer['server_key'];
        $login = $serverKey === 'xilero'
            ? XileRO_Login::find($this->selectedPlayer['account_id'])
            : XileRetro_Login::find($this->selectedPlayer['account_id']);

        if (! $login) {
            Notification::make()
                ->title('Game account not found')
                ->danger()
                ->send();

            return;
        }

        // Hash the password using the game server's method
        $hashedPassword = MakeHashedLoginPassword::run($password, $serverKey);

        $login->update([
            'user_pass' => $hashedPassword,
        ]);

        // Also update the synced GameAccount if it exists
        $gameAccount = GameAccount::where('server', $serverKey)
            ->where('ragnarok_account_id', $login->account_id)
            ->first();

        if ($gameAccount) {
            $gameAccount->update([
                'user_pass' => $hashedPassword,
            ]);
        }

        Notification::make()
            ->title('Game password reset')
            ->body("New password for {$login->userid} ({$this->selectedPlayer['server']}): {$password}")
            ->success()
            ->persistent()
            ->send();

        $this->newGamePassword = '';
    }

    /**
     * @return array<int, array{id: int, name: string, email: string}>
     */
    public function getMasterAccountOptions(): array
    {
        return User::orderBy('name')
            ->limit(100)
            ->get(['id', 'name', 'email'])
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ])
            ->toArray();
    }
}
