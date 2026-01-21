<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DownloadResource\Pages\CreateDownload;
use App\Filament\Resources\DownloadResource\Pages\EditDownload;
use App\Filament\Resources\DownloadResource\Pages\ListDownloads;
use App\Models\Download;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DownloadResource extends Resource
{
    protected static ?string $model = Download::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Download';

    protected static ?string $pluralModelLabel = 'Downloads';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Download Information')
                    ->description('Configure the basic settings for this download')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->label('Download Type')
                                    ->options(Download::TYPES)
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->default(Download::TYPE_FULL)
                                    ->helperText('Full Client for PC, Android for mobile')
                                    ->required()
                                    ->live(),

                                TextInput::make('name')
                                    ->label('Display Name')
                                    ->placeholder('e.g., Full Client from Google Drive v8 (3GB)')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('The name shown on the download button'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('version')
                                    ->label('Version')
                                    ->placeholder('e.g., 8.0')
                                    ->maxLength(50)
                                    ->helperText('Optional version number'),

                                Select::make('button_style')
                                    ->label('Button Style')
                                    ->options(Download::BUTTON_STYLES)
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->default(Download::BUTTON_STYLE_PRIMARY)
                                    ->helperText('Visual style of the download button')
                                    ->required(),

                                TextInput::make('display_order')
                                    ->label('Display Order')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Lower numbers appear first'),
                            ]),
                    ]),

                Section::make('Download Source')
                    ->description('Provide either an external link or upload an APK file (for Android)')
                    ->icon('heroicon-o-link')
                    ->schema([
                        TextInput::make('link')
                            ->label('External Link')
                            ->placeholder('https://drive.google.com/...')
                            ->url()
                            ->maxLength(2048)
                            ->helperText('External download link (Google Drive, Mega, Discord CDN, etc.)'),

                        FileUpload::make('file')
                            ->label('APK File')
                            ->disk('android_apk')
                            ->directory('')
                            ->maxSize(512000) // 500MB
                            ->acceptedFileTypes(['application/vnd.android.package-archive', '.apk'])
                            ->downloadable()
                            ->helperText('Upload an APK file directly (max 500MB). If provided, this will be used instead of the external link.')
                            ->preserveFilenames()
                            ->storeFileNamesIn('file_name')
                            ->visible(fn (Get $get): bool => $get('type') === Download::TYPE_ANDROID),
                    ]),

                Section::make('Visibility')
                    ->description('Control whether this download is shown on the homepage')
                    ->icon('heroicon-o-eye')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Enabled')
                            ->helperText('Disabled downloads will not appear on the homepage')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_order')
                    ->label('#')
                    ->sortable()
                    ->badge(),

                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Download::TYPE_FULL => 'success',
                        Download::TYPE_ANDROID => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Download::TYPES[$state] ?? $state),

                TextColumn::make('name')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($state) => $state),

                TextColumn::make('version')
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('button_style')
                    ->label('Style')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Download::BUTTON_STYLE_PRIMARY => 'primary',
                        Download::BUTTON_STYLE_SECONDARY => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Download::BUTTON_STYLES[$state] ?? $state),

                IconColumn::make('file')
                    ->label('Source')
                    ->icon(fn ($state): string => $state ? 'heroicon-o-document-arrow-down' : 'heroicon-o-link')
                    ->color(fn ($state): string => $state ? 'success' : 'info')
                    ->tooltip(fn (Download $record): string => $record->file ? 'Uploaded file: '.$record->file_name : 'External link'),

                ToggleColumn::make('enabled')
                    ->label('Active'),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j')
                    ->sortable()
                    ->tooltip(fn ($state) => $state?->format('M j, Y g:i A')),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Download Type')
                    ->options(Download::TYPES),
                SelectFilter::make('enabled')
                    ->label('Status')
                    ->options([
                        '1' => 'Enabled',
                        '0' => 'Disabled',
                    ]),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Open')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (Download $record): ?string => $record->download_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Download $record): bool => ! empty($record->download_url)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('display_order')
            ->defaultSort('display_order')
            ->paginated([10, 25, 50]);
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
            'index' => ListDownloads::route('/'),
            'create' => CreateDownload::route('/create'),
            'edit' => EditDownload::route('/{record}/edit'),
        ];
    }
}
