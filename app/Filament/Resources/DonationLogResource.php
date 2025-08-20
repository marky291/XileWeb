<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\DonationLogResource\Pages\ListDonationLogs;
use App\Filament\Resources\DonationLogResource\Pages\CreateDonationLog;
use App\Filament\Resources\DonationLogResource\Pages\EditDonationLog;
use App\Filament\Resources\DonationLogResource\Pages;
use App\Filament\Resources\DonationLogResource\RelationManagers;
use App\Ragnarok\DonationLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationLogResource extends Resource
{
    protected static ?string $model = DonationLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string | \UnitEnum | null $navigationGroup = 'Uber System';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('login.userid')->readOnly(),
                TextInput::make('player')->readOnly(),
                TextInput::make('item_name')->readOnly(),
                TextInput::make('refine')->readOnly(),
                TextInput::make('amount')->readOnly(),
                TextInput::make('spend')->readOnly(),
                TextInput::make('balance')->readOnly(),
                TextInput::make('timestamp')->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('login.userid'),
                TextColumn::make('player'),
                TextColumn::make('item_name'),
                TextColumn::make('refine'),
                TextColumn::make('amount'),
                TextColumn::make('spend'),
                TextColumn::make('balance'),
                TextColumn::make('timestamp'),
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
            'index' => ListDonationLogs::route('/'),
            'create' => CreateDonationLog::route('/create'),
            'edit' => EditDonationLog::route('/{record}/edit'),
        ];
    }
}
