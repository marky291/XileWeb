<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CharResource\Pages;
use App\Filament\Resources\CharResource\RelationManagers;
use App\Filament\Resources\LoginResource\RelationManagers\LoginRelationManager;
use App\Ragnarok\Char;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CharResource extends Resource
{
    protected static ?string $model = Char::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Player Management';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_id'),
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('last_login'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_id'),
                Tables\Columns\TextColumn::make('login.userid')->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Character')->searchable(),
                Tables\Columns\TextColumn::make('last_login'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ])
            ->emptyStateActions([
                //Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LoginRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChars::route('/'),
            'create' => Pages\CreateChar::route('/create'),
            'edit' => Pages\EditChar::route('/{record}/edit'),
        ];
    }
}
