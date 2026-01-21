<?php

namespace App\Filament\Resources\PatchResource\Widgets;

use App\Models\Item;
use App\Models\Patch;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PatchCompileStatus extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '3s';

    public ?Patch $record = null;

    protected function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        $patch = $this->record->fresh();

        if ($patch->is_compiling) {
            return [
                Stat::make('Status', 'Compiling...')
                    ->description('Processing patch file')
                    ->color('warning')
                    ->icon('heroicon-o-arrow-path'),
            ];
        }

        if ($patch->compiled_at === null) {
            return [
                Stat::make('Status', 'Not Compiled')
                    ->description('Click compile to process')
                    ->color('gray')
                    ->icon('heroicon-o-clock'),
            ];
        }

        // Count items for this patch
        $dataItemCount = Item::where('data_patch_id', $patch->id)->count();
        $spriteItemCount = Item::where('sprite_patch_id', $patch->id)->count();

        $stats = [
            Stat::make('Status', 'Compiled')
                ->description($patch->compiled_at->format('M j, Y g:i A'))
                ->color('success')
                ->icon('heroicon-o-check-circle'),
        ];

        if ($patch->type === 'FLD') {
            $stats[] = Stat::make('Items Updated', $dataItemCount)
                ->description('ItemInfo data extracted')
                ->color('info')
                ->icon('heroicon-o-document-text');
        } else {
            $stats[] = Stat::make('Sprites Extracted', $spriteItemCount)
                ->description('Item images updated')
                ->color('success')
                ->icon('heroicon-o-photo');
        }

        return $stats;
    }

    public function getPollingInterval(): ?string
    {
        if (! $this->record) {
            return null;
        }

        $patch = $this->record->fresh();

        // Only poll when compiling
        return $patch->is_compiling ? $this->pollingInterval : null;
    }
}
