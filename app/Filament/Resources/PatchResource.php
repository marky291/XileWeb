<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatchResource\Pages;
use App\Filament\Resources\PatchResource\RelationManagers;
use App\Models\Patch;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Patch';
    public static function form(Form $form): Form
    {
        $last_patch = Patch::latest('number')->first();

        return $form
            ->columns(1)->schema([
                Select::make('type')->label('Patch Type')
                    ->options([
                        'FLD' => 'FLD',
                        'GRF' => 'GRF',
                    ])->required(),
                Forms\Components\TextInput::make('number')->label('Patch Number')->default($last_patch->number + 1)->readOnlyOn('create')->unique(),
                Forms\Components\FileUpload::make('patch_name')->label('Patch File')
                    ->required()->preserveFilenames()->unique(),
                Forms\Components\TextInput::make('comments'),
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
                Tables\Columns\TextColumn::make('number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('patch_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comments'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListPatches::route('/'),
            'create' => Pages\CreatePatch::route('/create'),
            'edit' => Pages\EditPatch::route('/{record}/edit'),
        ];
    }
}
