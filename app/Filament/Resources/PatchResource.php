<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatchResource\Pages\CreatePatch;
use App\Filament\Resources\PatchResource\Pages\EditPatch;
use App\Filament\Resources\PatchResource\Pages\ListPatches;
use App\Models\Patch;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PatchResource extends Resource
{
    protected static ?string $model = Patch::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationGroup = 'Client Patch';

    protected static ?string $modelLabel = 'Client Patch';

    protected static ?string $pluralModelLabel = 'Client Patches';

    public static function form(Form $form): Form
    {
        $last_patch = Patch::latest('number')->first();
        $next_number = $last_patch ? $last_patch->number + 1 : 1;
        $recent_patches = Patch::latest('number')->take(5)->get();

        return $form
            ->schema([
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
                                TextInput::make('number')
                                    ->label('Patch Number')
                                    ->default($next_number)
                                    ->disabled()
                                    ->dehydrated()
                                    ->prefix('#')
                                    ->helperText('Auto-incremented patch version number'),
                            ]),
                    ]),

                Section::make('Upload Patch File')
                    ->description('Select the .gpf patch file to upload')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->schema([
                        FileUpload::make('file')
                            ->label('Patch File')
                            ->acceptedFileTypes(['application/octet-stream', '.gpf'])
                            ->directory('patches')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->downloadable()
                            ->openable()
                            ->helperText('Upload a single .gpf patch file (Required)')
                            ->required()
                            ->maxSize(102400)
                            ->uploadingMessage('Uploading patch file...')
                            ->removeUploadedFileButtonPosition('right'),
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

                Section::make('Recent Patches')
                    ->description('Last 5 patches for reference')
                    ->icon('heroicon-o-clock')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('recent_patches')
                            ->label('')
                            ->content(function () use ($recent_patches) {
                                if ($recent_patches->isEmpty()) {
                                    return new HtmlString('<p class="text-sm text-gray-500">No patches found</p>');
                                }

                                $html = '<div class="space-y-2">';
                                foreach ($recent_patches as $patch) {
                                    $type_badge = $patch->type === 'FLD'
                                        ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">FLD</span>'
                                        : '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">GRF</span>';

                                    $file_name = $patch->file ? basename($patch->file) : 'No file';
                                    $comments = $patch->comments ? ' - '.Str::limit($patch->comments, 50) : '';

                                    $html .= sprintf(
                                        '<div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                            <span class="font-mono text-sm font-semibold">#%d</span>
                                            %s
                                            <span class="text-sm text-gray-600 dark:text-gray-400">%s%s</span>
                                        </div>',
                                        $patch->number,
                                        $type_badge,
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
                    ->label('Patch #')
                    ->numeric()
                    ->sortable()
                    ->badge(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'FLD' => 'success',
                        'GRF' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'FLD' => 'FLD (Root)',
                        'GRF' => 'GRF (File)',
                        default => $state,
                    })
                    ->searchable(),
                TextColumn::make('file')
                    ->label('Patch File')
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return 'No file';
                        }

                        return basename($state);
                    })
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Filename copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('comments')
                    ->label('Notes')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    }),
                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Patch $record): string => $record->file ? asset('storage/'.$record->file) : '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Patch $record): bool => ! empty($record->file)),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('number', 'desc')
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
