<?php

namespace App\Livewire;

use App\Ragnarok\Login;
use App\Ragnarok\Vending;
use App\Ragnarok\VendingItems;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class ServerVendings extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function placeholder()
    {
        return <<<'HTML'
        <div>
            <div class="text-3xl text-gray-100 animate-ping">
                Loading...
            </div>
        </div>
        HTML;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(VendingItems::query())
            ->columns([
                TextColumn::make('cartinventory_id')->label('Item')->searchable()->sortable(),
                TextColumn::make('amount')->label('Amount')->searchable()->sortable(),
                TextColumn::make('price')->label('Price')->numeric(decimalPlaces: 0,
                    decimalSeparator: '.',
                    thousandsSeparator: ','),
                TextColumn::make('vending.title')->label('Vendor Shop')->searchable()->sortable(),
                TextColumn::make('vending.map')->searchable()->sortable(),
                TextColumn::make('vending.x'),
                TextColumn::make('vending.y'),
            ])
            ->filters([
                Filter::make('Less than 1m')->query(fn (Builder $query): Builder => $query->where('price', '<', 1000000)),
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.server-vendings');
    }
}
