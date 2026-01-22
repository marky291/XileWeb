<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Jobs\SendPostToDiscord;
use App\Models\Item;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 0;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Post Details')
                    ->schema([
                        Select::make('client')
                            ->label('Client')
                            ->options(Post::CLIENTS)
                            ->native(false)
                            ->selectablePlaceholder(false)
                            ->default(Post::CLIENT_XILERO)
                            ->helperText('Select which client this post is for')
                            ->live()
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
                        FileUpload::make('image')
                            ->label('Post Image')
                            ->image()
                            ->disk('public')
                            ->directory('post-images')
                            ->maxSize(5120)
                            ->helperText('Featured image displayed on the homepage and article page (max 5MB)'),
                    ]),

                Section::make('Featured Items')
                    ->description('Use #itemId in article (e.g. #5013) then click "Extract" to replace with "Item Name [5013]" and add to featured items.')
                    ->collapsed()
                    ->headerActions([
                        Action::make('extractFromArticle')
                            ->label('Extract from Article')
                            ->icon('heroicon-o-magnifying-glass')
                            ->color('gray')
                            ->action(function (Get $get, Set $set): void {
                                $articleContent = $get('article_content') ?? '';
                                $client = $get('client') ?? Post::CLIENT_XILERO;
                                $isRetro = $client === Post::CLIENT_RETRO;

                                // Extract item IDs from article (pattern: #12345)
                                preg_match_all('/#(\d{4,5})\b/', $articleContent, $matches);

                                $itemIds = collect($matches[1])->unique()->values();

                                if ($itemIds->isEmpty()) {
                                    Notification::make()
                                        ->title('No item IDs found')
                                        ->body('Use #itemId format in article (e.g. #5013).')
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                // Find items matching these IDs for the correct client
                                $items = Item::where('is_xileretro', $isRetro)
                                    ->whereIn('item_id', $itemIds)
                                    ->get()
                                    ->keyBy('item_id');

                                if ($items->isEmpty()) {
                                    Notification::make()
                                        ->title('No matching items')
                                        ->body('Found IDs but no matching items in the database.')
                                        ->warning()
                                        ->send();

                                    return;
                                }

                                // Replace #itemId with "item_name [item_id]" in article content
                                $updatedContent = preg_replace_callback('/#(\d{4,5})\b/', function ($match) use ($items) {
                                    $itemId = $match[1];
                                    $item = $items->get($itemId);

                                    return $item ? "{$item->name} [{$item->item_id}]" : $match[0];
                                }, $articleContent);

                                $set('article_content', $updatedContent);

                                // Get existing item IDs to avoid duplicates
                                $existingItemIds = collect($get('itemPosts') ?? [])
                                    ->pluck('item_id')
                                    ->filter()
                                    ->toArray();

                                // Add new items to featured
                                $currentItems = $get('itemPosts') ?? [];
                                $addedCount = 0;

                                foreach ($items as $item) {
                                    if (! in_array($item->id, $existingItemIds)) {
                                        $currentItems[] = ['item_id' => $item->id];
                                        $addedCount++;
                                    }
                                }

                                $set('itemPosts', $currentItems);

                                $replacedCount = $items->count();
                                Notification::make()
                                    ->title('Items processed')
                                    ->body("Replaced {$replacedCount} item ID(s) with names. Added {$addedCount} new item(s) to featured.")
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->schema([
                        Repeater::make('itemPosts')
                            ->relationship()
                            ->label('')
                            ->simple(
                                Select::make('item_id')
                                    ->label('')
                                    ->placeholder('Search by name or ID...')
                                    ->searchable()
                                    ->getSearchResultsUsing(function (string $search, Get $get) {
                                        $client = $get('../../client') ?? Post::CLIENT_XILERO;
                                        $isRetro = $client === Post::CLIENT_RETRO;

                                        return Item::where('is_xileretro', $isRetro)
                                            ->where(function ($query) use ($search) {
                                                $query->where('name', 'like', "%{$search}%")
                                                    ->orWhere('item_id', 'like', "%{$search}%");
                                            })
                                            ->orderBy('item_id')
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(fn (Item $item) => [
                                                $item->id => "#{$item->item_id} - {$item->name}",
                                            ]);
                                    })
                                    ->getOptionLabelUsing(function ($value): ?string {
                                        $item = Item::find($value);

                                        return $item ? "#{$item->item_id} - {$item->name}" : null;
                                    })
                                    ->required()
                            )
                            ->orderColumn('sort_order')
                            ->reorderable()
                            ->addActionLabel('Add Item')
                            ->defaultItems(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->disk('public')
                    ->square()
                    ->size(40),
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
            ->recordActions([
                Action::make('sendToDiscord')
                    ->label('Discord')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Send to Discord')
                    ->modalDescription('This will send the post to the Discord news channel.')
                    ->action(function (Post $record): void {
                        SendPostToDiscord::dispatch($record);

                        Notification::make()
                            ->title('Sent to Discord')
                            ->body('The post has been queued for Discord.')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
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
