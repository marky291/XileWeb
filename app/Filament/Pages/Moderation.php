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
                        'xilero' => 'XileRO',
                        'xileretro' => 'XileRetro',
                    ])
                    ->default('xilero')
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadRecentLogins()),
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
        return $this->data['server'] ?? 'xilero';
    }

    protected function getSearch(): string
    {
        return $this->data['search'] ?? '';
    }

    public function loadRecentLogins(): void
    {
        try {
            $query = $this->getServer() === 'xilero'
                ? XileRO_Login::query()
                : XileRetro_Login::query();

            $this->recentLogins = $query
                ->whereNotNull('lastlogin')
                ->orderByDesc('lastlogin')
                ->limit(20)
                ->get()
                ->map(fn ($login) => [
                    'account_id' => $login->account_id,
                    'userid' => $login->userid,
                    'email' => $login->email,
                    'last_ip' => $login->last_ip,
                    'lastlogin' => $login->lastlogin,
                    'logincount' => $login->logincount,
                    'state' => $login->state,
                    'unban_time' => $login->unban_time,
                    'group_id' => $login->group_id,
                ])
                ->toArray();
        } catch (Exception $e) {
            $this->recentLogins = [];
            Notification::make()
                ->title('Database Error')
                ->body('Could not load login data: '.$e->getMessage())
                ->danger()
                ->send();
        }
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
            $query = $this->getServer() === 'xilero'
                ? XileRO_Login::query()
                : XileRetro_Login::query();

            $this->recentLogins = $query
                ->where(function ($q) use ($searchTerm) {
                    $q->where('userid', 'like', $searchTerm)
                        ->orWhere('last_ip', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm);
                })
                ->orderByDesc('lastlogin')
                ->limit(50)
                ->get()
                ->map(fn ($login) => [
                    'account_id' => $login->account_id,
                    'userid' => $login->userid,
                    'email' => $login->email,
                    'last_ip' => $login->last_ip,
                    'lastlogin' => $login->lastlogin,
                    'logincount' => $login->logincount,
                    'state' => $login->state,
                    'unban_time' => $login->unban_time,
                    'group_id' => $login->group_id,
                ])
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
        $this->selectedAccount = $this->recentLogins[$index] ?? null;
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
