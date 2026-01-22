<?php

namespace App\Filament\Pages;

use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Login;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class Moderation extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.moderation';

    public ?array $data = [];

    public ?array $recentLogins = [];

    public ?array $selectedAccount = null;

    public ?int $banDuration = 24;

    public ?string $banReason = '';

    public int $page = 1;

    public int $perPage = 20;

    public int $totalResults = 0;

    public function mount(): void
    {
        $this->form->fill();
        $this->loadRecentLogins();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('server')
                    ->label('Server')
                    ->options([
                        'all' => 'All Servers',
                        'xilero' => 'XileRO',
                        'xileretro' => 'XileRetro',
                    ])
                    ->default('all')
                    ->live()
                    ->afterStateUpdated(function () {
                        $this->page = 1;
                        $this->selectedAccount = null;
                        $this->loadRecentLogins();
                    }),
                TextInput::make('search')
                    ->label('Search Account')
                    ->placeholder('Username or IP address...')
                    ->minLength(2),
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function getServer(): string
    {
        return $this->data['server'] ?? 'all';
    }

    protected function getSearch(): string
    {
        return $this->data['search'] ?? '';
    }

    public function loadRecentLogins(): void
    {
        try {
            $server = $this->getServer();
            $results = collect();

            $mapLogin = fn ($login, $serverName) => [
                'account_id' => $login->account_id,
                'userid' => $login->userid,
                'email' => $login->email,
                'last_ip' => $login->last_ip,
                'lastlogin' => $login->lastlogin,
                'logincount' => $login->logincount,
                'state' => $login->state,
                'unban_time' => $login->unban_time,
                'group_id' => $login->group_id,
                'server' => $serverName,
            ];

            // Fetch enough records to paginate (fetch more than needed for merged sorting)
            $fetchLimit = $this->perPage * ($this->page + 1);

            if ($server === 'all' || $server === 'xilero') {
                $xileroLogins = XileRO_Login::query()
                    ->whereNotNull('lastlogin')
                    ->orderByDesc('lastlogin')
                    ->limit($fetchLimit)
                    ->get()
                    ->map(fn ($login) => $mapLogin($login, 'xilero'));
                $results = $results->merge($xileroLogins);
            }

            if ($server === 'all' || $server === 'xileretro') {
                $xileretroLogins = XileRetro_Login::query()
                    ->whereNotNull('lastlogin')
                    ->orderByDesc('lastlogin')
                    ->limit($fetchLimit)
                    ->get()
                    ->map(fn ($login) => $mapLogin($login, 'xileretro'));
                $results = $results->merge($xileretroLogins);
            }

            $sorted = $results->sortByDesc('lastlogin')->values();
            $this->totalResults = $sorted->count();

            $this->recentLogins = $sorted
                ->skip(($this->page - 1) * $this->perPage)
                ->take($this->perPage)
                ->values()
                ->toArray();
        } catch (Exception $e) {
            $this->recentLogins = [];
            $this->totalResults = 0;
            Notification::make()
                ->title('Database Error')
                ->body('Could not load login data: '.$e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->selectedAccount = null;
            $this->loadRecentLogins();
        }
    }

    public function nextPage(): void
    {
        if ($this->page < $this->getTotalPages()) {
            $this->page++;
            $this->selectedAccount = null;
            $this->loadRecentLogins();
        }
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalResults / $this->perPage);
    }

    public function searchAccounts(): void
    {
        $search = $this->getSearch();

        if (strlen($search) < 2) {
            Notification::make()
                ->title('Search term too short')
                ->body('Please enter at least 2 characters')
                ->warning()
                ->send();

            return;
        }

        try {
            $searchTerm = '%'.$search.'%';
            $server = $this->getServer();
            $results = collect();

            $mapLogin = fn ($login, $serverName) => [
                'account_id' => $login->account_id,
                'userid' => $login->userid,
                'email' => $login->email,
                'last_ip' => $login->last_ip,
                'lastlogin' => $login->lastlogin,
                'logincount' => $login->logincount,
                'state' => $login->state,
                'unban_time' => $login->unban_time,
                'group_id' => $login->group_id,
                'server' => $serverName,
            ];

            $searchQuery = function ($query) use ($searchTerm) {
                return $query->where(function ($q) use ($searchTerm) {
                    $q->where('userid', 'like', $searchTerm)
                        ->orWhere('last_ip', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm);
                })
                    ->orderByDesc('lastlogin')
                    ->limit(50);
            };

            if ($server === 'all' || $server === 'xilero') {
                $xileroLogins = $searchQuery(XileRO_Login::query())
                    ->get()
                    ->map(fn ($login) => $mapLogin($login, 'xilero'));
                $results = $results->merge($xileroLogins);
            }

            if ($server === 'all' || $server === 'xileretro') {
                $xileretroLogins = $searchQuery(XileRetro_Login::query())
                    ->get()
                    ->map(fn ($login) => $mapLogin($login, 'xileretro'));
                $results = $results->merge($xileretroLogins);
            }

            $this->recentLogins = $results
                ->sortByDesc('lastlogin')
                ->take(50)
                ->values()
                ->toArray();

            if (empty($this->recentLogins)) {
                Notification::make()
                    ->title('No results')
                    ->body('No accounts found matching your search')
                    ->info()
                    ->send();
            }
        } catch (Exception $e) {
            Notification::make()
                ->title('Search Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function selectAccount(int $index): void
    {
        $account = $this->recentLogins[$index] ?? null;

        // Toggle: if clicking the same account, deselect it
        if ($this->selectedAccount && ($this->selectedAccount['account_id'] ?? null) === ($account['account_id'] ?? null)) {
            $this->selectedAccount = null;
        } else {
            $this->selectedAccount = $account;
        }
    }

    public function clearSelection(): void
    {
        $this->selectedAccount = null;
        $this->banDuration = 24;
        $this->banReason = '';
    }

    public function banAccount(): void
    {
        if (! $this->selectedAccount) {
            return;
        }

        try {
            $login = $this->getServer() === 'xilero'
                ? XileRO_Login::find($this->selectedAccount['account_id'])
                : XileRetro_Login::find($this->selectedAccount['account_id']);

            if (! $login) {
                Notification::make()
                    ->title('Account not found')
                    ->danger()
                    ->send();

                return;
            }

            $unbanTime = now()->addHours($this->banDuration)->timestamp;

            $login->update([
                'state' => 5,
                'unban_time' => $unbanTime,
            ]);

            Notification::make()
                ->title('Account banned')
                ->body("{$login->userid} banned for {$this->banDuration} hours")
                ->success()
                ->send();

            $this->selectedAccount['state'] = 5;
            $this->selectedAccount['unban_time'] = $unbanTime;
            $this->loadRecentLogins();
        } catch (Exception $e) {
            Notification::make()
                ->title('Ban failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function unbanAccount(): void
    {
        if (! $this->selectedAccount) {
            return;
        }

        try {
            $login = $this->getServer() === 'xilero'
                ? XileRO_Login::find($this->selectedAccount['account_id'])
                : XileRetro_Login::find($this->selectedAccount['account_id']);

            if (! $login) {
                Notification::make()
                    ->title('Account not found')
                    ->danger()
                    ->send();

                return;
            }

            $login->update([
                'state' => 0,
                'unban_time' => 0,
            ]);

            Notification::make()
                ->title('Account unbanned')
                ->body("{$login->userid} has been unbanned")
                ->success()
                ->send();

            $this->selectedAccount['state'] = 0;
            $this->selectedAccount['unban_time'] = 0;
            $this->loadRecentLogins();
        } catch (Exception $e) {
            Notification::make()
                ->title('Unban failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function getAccountStateLabel(int $state): string
    {
        return match ($state) {
            0 => 'Active',
            1 => 'Server Locked',
            2 => 'Frozen',
            3 => 'Password Locked',
            4 => 'Blocked',
            5 => 'Banned',
            default => "Unknown ({$state})",
        };
    }

    public function getAccountStateColor(int $state): string
    {
        return match ($state) {
            0 => 'success',
            5 => 'danger',
            default => 'warning',
        };
    }
}
