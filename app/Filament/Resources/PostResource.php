<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Models\Post;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Select::make('client')
                    ->label('Client')
                    ->options(Post::CLIENTS)
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->default(Post::CLIENT_XILERO)
                    ->helperText('Select which client this post is for')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->helperText('The title of the post'),
                Textarea::make('patcher_notice')
                    ->label('Patcher Notice')
                    ->required()
                    ->rows(3)
                    ->maxLength(500)
                    ->placeholder('Short notice text that will appear in the game patcher...')
                    ->helperText('This text appears in the game patcher - keep it brief and informative'),
                MarkdownEditor::make('article_content')
                    ->label('Full Article')
                    ->required()
                    ->placeholder('Full article content that users see when they click "Read More" on the website...')
                    ->helperText('Complete article content for the website (supports Markdown formatting)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'retro' => 'danger',
                        'xilero' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'retro' => 'Retro',
                        'xilero' => 'XileRO',
                        default => $state,
                    }),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('patcher_notice')
                    ->label('Patcher Notice')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('client')
                    ->label('Client')
                    ->options(Post::CLIENTS),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
