<?php

namespace App\Filament\Widgets;

use App\Models\UberShopItem;
use App\Models\User;
use App\XileRetro\XileRetro_Char;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_Char;
use App\XileRO\XileRO_Login;
use Exception;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ServerStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        return [
            $this->getMasterAccountsStat(),
            $this->getXileROStat(),
            $this->getXileRetroStat(),
            $this->getUberShopStat(),
        ];
    }

    protected function getMasterAccountsStat(): Stat
    {
        $total = User::count();
        $today = User::whereDate('created_at', today())->count();
        $thisWeek = User::where('created_at', '>=', now()->subWeek())->count();

        return Stat::make('Master Accounts', number_format($total))
            ->description($today > 0 ? "+{$today} today, +{$thisWeek} this week" : "+{$thisWeek} this week")
            ->descriptionIcon('heroicon-m-user-plus')
            ->color('primary')
            ->chart($this->getRecentRegistrations());
    }

    protected function getXileROStat(): Stat
    {
        try {
            $online = XileRO_Char::where('online', 1)->count();
            $totalAccounts = XileRO_Login::count();
            $totalChars = XileRO_Char::count();

            return Stat::make('XileRO', "{$online} Online")
                ->description("{$totalAccounts} accounts, {$totalChars} characters")
                ->descriptionIcon('heroicon-m-signal')
                ->color($online > 0 ? 'success' : 'gray');
        } catch (Exception $e) {
            return Stat::make('XileRO', 'N/A')
                ->description('Database unavailable')
                ->color('danger');
        }
    }

    protected function getXileRetroStat(): Stat
    {
        try {
            $online = XileRetro_Char::where('online', 1)->count();
            $totalAccounts = XileRetro_Login::count();
            $totalChars = XileRetro_Char::count();

            return Stat::make('XileRetro', "{$online} Online")
                ->description("{$totalAccounts} accounts, {$totalChars} characters")
                ->descriptionIcon('heroicon-m-signal')
                ->color($online > 0 ? 'success' : 'gray');
        } catch (Exception $e) {
            return Stat::make('XileRetro', 'N/A')
                ->description('Database unavailable')
                ->color('danger');
        }
    }

    protected function getUberShopStat(): Stat
    {
        $totalItems = UberShopItem::count();
        $activeItems = UberShopItem::where('enabled', true)->count();
        $totalUbers = User::sum('uber_balance');

        return Stat::make('Uber Shop', number_format($activeItems).' Items')
            ->description(number_format($totalUbers).' Ubers in circulation')
            ->descriptionIcon('heroicon-m-shopping-bag')
            ->color('warning');
    }

    /**
     * @return array<int>
     */
    protected function getRecentRegistrations(): array
    {
        $registrations = User::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return count($registrations) > 0 ? $registrations : [0];
    }
}
