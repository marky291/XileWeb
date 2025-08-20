<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\PatchResource\Pages\ListPatches;
use App\Filament\Resources\PatchResource\Pages\CreatePatch;
use App\Filament\Resources\PatchResource\Pages\EditPatch;
use App\Filament\Resources\PatchResource\Pages;
use App\Filament\Resources\PatchResource\RelationManagers;
use App\Models\Patch;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PatchResource extends Resource
{
    protected static ?string $model = Patch::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static string | \UnitEnum | null $navigationGroup = 'Patch';
    public static function form(Schema $schema): Schema
    {
        $last_patch = Patch::latest('number')->first();

        return $schema
            ->columns(1)->components([
                Select::make('type')->label('Patch Type')
                    ->options([
                        'FLD' => 'FLD',
                        'GRF' => 'GRF',
                    ])->required(),
                TextInput::make('number')->label('Patch Number')->default($last_patch->number + 1)->readOnlyOn('create')->unique(),
                FileUpload::make('patch_name')->label('Patch File')
                    ->required()->preserveFilenames()->unique(),
                TextInput::make('comments'),
        ]);
    }

    protected function afterFill(): void
    {
        dd($this);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('patch_name')
                    ->searchable(),
                TextColumn::make('comments'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatches::route('/'),
            'create' => CreatePatch::route('/create'),
            'edit' => EditPatch::route('/{record}/edit'),
        ];
    }
}
