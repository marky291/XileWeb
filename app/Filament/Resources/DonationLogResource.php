<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationLogResource\Pages\CreateDonationLog;
use App\Filament\Resources\DonationLogResource\Pages\EditDonationLog;
use App\Filament\Resources\DonationLogResource\Pages\ListDonationLogs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DonationLogResource extends Resource
{
    protected static ?string $model = DonationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Uber System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
