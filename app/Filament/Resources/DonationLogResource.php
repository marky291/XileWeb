<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonationLogResource\Pages;
use App\Filament\Resources\DonationLogResource\RelationManagers;
use App\Ragnarok\DonationLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DonationLogResource extends Resource
{
    protected static ?string $model = DonationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Uber System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('login.userid')->readOnly(),
                Forms\Components\TextInput::make('player')->readOnly(),
                Forms\Components\TextInput::make('item_name')->readOnly(),
                Forms\Components\TextInput::make('refine')->readOnly(),
                Forms\Components\TextInput::make('amount')->readOnly(),
                Forms\Components\TextInput::make('spend')->readOnly(),
                Forms\Components\TextInput::make('balance')->readOnly(),
                Forms\Components\TextInput::make('timestamp')->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('login.userid'),
                Tables\Columns\TextColumn::make('player'),
                Tables\Columns\TextColumn::make('item_name'),
                Tables\Columns\TextColumn::make('refine'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('spend'),
                Tables\Columns\TextColumn::make('balance'),
                Tables\Columns\TextColumn::make('timestamp'),
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
            'index' => Pages\ListDonationLogs::route('/'),
            'create' => Pages\CreateDonationLog::route('/create'),
            'edit' => Pages\EditDonationLog::route('/{record}/edit'),
        ];
    }
}
