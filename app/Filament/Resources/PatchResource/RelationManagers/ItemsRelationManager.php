<?php

namespace App\Filament\Resources\PatchResource\RelationManagers;

use App\Models\Item;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'dataItems';

    protected static ?string $title = 'Items Updated';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->compiled_at !== null;
    }

    public function table(Table $table): Table
    {
        $patchId = $this->getOwnerRecord()->id;
        $patchType = $this->getOwnerRecord()->type;

        return $table
            ->query(function () use ($patchId): Builder {
                return Item::query()
                    ->where(function (Builder $query) use ($patchId) {
                        $query->where('data_patch_id', $patchId)
                            ->orWhere('sprite_patch_id', $patchId);
                    });
            })
            ->columns([
                ImageColumn::make('icon_image')
                    ->label('')
                    ->getStateUsing(fn (Item $record): string => ($record->is_xileretro ? 'retro' : 'xilero').'/item/'.$record->item_id.'.png')
                    ->disk('public')
                    ->visibility('public')
                    ->width(24)
                    ->height(24)
                    ->checkFileExistence(false),

                TextColumn::make('item_id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->limit(30),

                TextColumn::make('resource_name')
                    ->label('Resource')
                    ->sortable()
                    ->searchable()
                    ->limit(20)
                    ->toggleable(),

                TextColumn::make('update_type')
                    ->label('Updated')
                    ->badge()
                    ->state(function (Item $record) use ($patchId): string {
                        $types = [];
                        if ($record->data_patch_id === $patchId) {
                            $types[] = 'Data';
                        }
                        if ($record->sprite_patch_id === $patchId) {
                            $types[] = 'Sprites';
                        }

                        return implode(' & ', $types);
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Data' => 'info',
                        'Sprites' => 'success',
                        'Data & Sprites' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('slots')
                    ->label('Slots')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('view_id')
                    ->label('View ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('update_type')
                    ->label('Update Type')
                    ->options([
                        'data' => 'Data Only',
                        'sprites' => 'Sprites Only',
                        'both' => 'Both',
                    ])
                    ->query(function (Builder $query, array $data) use ($patchId): Builder {
                        return match ($data['value']) {
                            'data' => $query->where('data_patch_id', $patchId)->where(function ($q) use ($patchId) {
                                $q->whereNull('sprite_patch_id')->orWhere('sprite_patch_id', '!=', $patchId);
                            }),
                            'sprites' => $query->where('sprite_patch_id', $patchId)->where(function ($q) use ($patchId) {
                                $q->whereNull('data_patch_id')->orWhere('data_patch_id', '!=', $patchId);
                            }),
                            'both' => $query->where('data_patch_id', $patchId)->where('sprite_patch_id', $patchId),
                            default => $query,
                        };
                    }),
            ])
            ->defaultSort('item_id')
            ->paginated([10, 25, 50, 100]);
    }
}
