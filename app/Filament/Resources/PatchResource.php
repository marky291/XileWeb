<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatchResource\Pages\CreatePatch;
use App\Filament\Resources\PatchResource\Pages\EditPatch;
use App\Filament\Resources\PatchResource\Pages\ListPatches;
use App\Models\Patch;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PatchResource extends Resource
{
    protected static ?string $model = Patch::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Client Patch';

    protected static ?string $pluralModelLabel = 'Client Patches';

    public static function form(Schema $schema): Schema
    {

        return $schema
            ->components([
                Section::make('Patch Information')
                    ->description('Configure the basic settings for your patch')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Patch Type')
                                    ->options([
                                        'FLD' => 'FLD - Root Folder Patch',
                                        'GRF' => 'GRF - GRF File Patch',
                                    ])
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->default('FLD')
                                    ->helperText('FLD patches to the Root folder, GRF patches to the GRF file')
                                    ->required(),
                                Select::make('client')
                                    ->label('Client')
                                    ->options(Patch::CLIENTS)
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->default(Patch::CLIENT_XILERO)
                                    ->helperText('Select which client this patch is for')
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set) {
                                        $client = $state ?: Patch::CLIENT_XILERO;
                                        $maxNumber = Patch::where('client', $client)->max('number');
                                        $nextNumber = $maxNumber ? $maxNumber + 1 : 1;
                                        $set('number', $nextNumber);
                                    })
                                    ->required(),
                            ]),
                        TextInput::make('number')
                            ->label('Patch Number')
                            ->default(function (Get $get) {
                                $client = $get('client') ?: Patch::CLIENT_XILERO;
                                $maxNumber = Patch::where('client', $client)->max('number');

                                return $maxNumber ? $maxNumber + 1 : 1;
                            })
                            ->readOnly()
                            ->dehydrated()
                            ->prefix('#')
                            ->helperText('Auto-incremented patch version number'),
                    ]),

                Section::make('Upload Patch File')
                    ->description('Select the .gpf patch file to upload')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->schema([
                        FileUpload::make('file')
                            ->label('Patch File')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->disk(function (Get $get): string {
                                $client = $get('client') ?? 'xilero';

                                return match ($client) {
                                    'retro' => 'retro_patch',
                                    'xilero' => 'xilero_patch',
                                    default => 'xilero_patch'
                                };
                            })
                            ->directory('')
                            ->maxSize(102400)
                            ->downloadable()
                            ->helperText('Upload a .gpf patch file (max 100MB)')
                            ->preserveFilenames()
                            ->storeFileNamesIn('patch_name'),

                    ]),

                Section::make('Additional Information')
                    ->description('Add notes or comments about this patch')
                    ->icon('heroicon-o-pencil-square')
                    ->collapsed()
                    ->schema([
                        Textarea::make('comments')
                            ->label('Patch Notes')
                            ->placeholder('Describe what this patch contains, fixes, or changes...')
                            ->rows(3)
                            ->maxLength(255)
                            ->helperText('Optional notes about this patch (max 255 characters)'),
                    ]),

                Section::make('Create Announcement Post')
                    ->description('Optionally create a news post to announce this patch to players')
                    ->icon('heroicon-o-newspaper')
                    ->collapsed()
                    ->schema([
                        Toggle::make('create_post')
                            ->label('Create announcement post for this patch')
                            ->helperText('Enable this to create a news post that players can read')
                            ->live()
                            ->default(false),

                        Grid::make(1)
                            ->schema([
                                TextInput::make('post_title')
                                    ->label('Post Title')
                                    ->placeholder(function (Get $get) {
                                        $client = $get('client') ?: Patch::CLIENT_XILERO;
                                        $maxNumber = Patch::where('client', $client)->max('number');
                                        $next_number = $maxNumber ? $maxNumber + 1 : 1;

                                        return 'e.g., Patch #'.$next_number.' - Halloween Update';
                                    })
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('The title of the announcement post'),

                                Textarea::make('post_patcher_notice')
                                    ->label('Patcher Notice')
                                    ->placeholder('Short notice about this patch for the game patcher...')
                                    ->rows(3)
                                    ->required()
                                    ->maxLength(500)
                                    ->helperText('This text appears in the game patcher - keep it brief and informative'),

                                MarkdownEditor::make('post_article_content')
                                    ->label('Full Article')
                                    ->required()
                                    ->placeholder('Full patch notes and details for the website...')
                                    ->toolbarButtons([
                                        'heading',
                                        'bold',
                                        'italic',
                                        'strike',
                                        'link',
                                        'orderedList',
                                        'unorderedList',
                                        'table',
                                        'undo',
                                        'redo',
                                    ])
                                    ->helperText('Complete article content for the website (supports Markdown formatting)'),
                            ])
                            ->visible(fn (Get $get): bool => $get('create_post') === true),
                    ])
                    ->visibleOn('create'),

                Section::make('Recent Patches')
                    ->description('Last 5 patches for reference')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('recent_patches')
                            ->label('')
                            ->content(function (Get $get) {
                                $client = $get('client') ?: Patch::CLIENT_XILERO;
                                $recent_patches = Patch::where('client', $client)->latest('number')->take(5)->get();

                                if ($recent_patches->isEmpty()) {
                                    return new HtmlString('<p class="text-sm text-gray-500">No patches found for '.($client === 'retro' ? 'Retro' : 'XileRO').'</p>');
                                }

                                $html = '<div class="space-y-2">';
                                foreach ($recent_patches as $patch) {
                                    $type_badge = $patch->type === 'FLD'
                                        ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">FLD</span>'
                                        : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">GRF</span>';

                                    $client_badge = $patch->client === 'retro'
                                        ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800">Retro</span>'
                                        : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">XileRO</span>';

                                    $file_name = $patch->file ? basename($patch->file) : 'No file';
                                    $comments = $patch->comments ? ' - '.Str::limit($patch->comments, 50) : '';

                                    $html .= sprintf(
                                        '<div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                            <span class="font-mono text-sm font-semibold">#%d</span>
                                            %s
                                            %s
                                            <span class="text-sm text-gray-600 dark:text-gray-400">%s%s</span>
                                        </div>',
                                        $patch->number,
                                        $type_badge,
                                        $client_badge,
                                        $file_name,
                                        $comments
                                    );
                                }
                                $html .= '</div>';

                                return new HtmlString($html);
                            }),
                    ])
                    ->visibleOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('#')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->prefix('#'),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'FLD' => 'success',
                        'GRF' => 'info',
                        default => 'gray',
                    }),

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

                TextColumn::make('patch_name')
                    ->label('File')
                    ->placeholder('No file')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Filename copied')
                    ->limit(20)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('comments')
                    ->label('Notes')
                    ->limit(30)
                    ->placeholder('No notes')
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 30 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('post.title')
                    ->label('Post')
                    ->placeholder('No post')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->post ? $record->post->title : null)
                    ->url(fn ($record) => $record->post ? "/posts/{$record->post->slug}" : null)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j')
                    ->sortable()
                    ->tooltip(fn ($state) => $state?->format('M j, Y g:i A')),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Patch Type')
                    ->options([
                        'FLD' => 'FLD - Root Folder',
                        'GRF' => 'GRF - GRF File',
                    ]),
                SelectFilter::make('client')
                    ->label('Client')
                    ->options(Patch::CLIENTS),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(function (Patch $record): string {
                        if (! $record->file) {
                            return '#';
                        }
                        $client = $record->client ?? 'xilero';
                        $disk = match ($client) {
                            'retro' => 'retro_patch',
                            'xilero' => 'xilero_patch',
                            default => 'xilero_patch'
                        };

                        // Use Storage facade to get the proper URL
                        return Storage::disk($disk)->url($record->file);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn (Patch $record): bool => ! empty($record->file)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100])
            ->poll('60s');
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
