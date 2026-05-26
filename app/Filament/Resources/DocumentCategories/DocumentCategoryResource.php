<?php

namespace App\Filament\Resources\DocumentCategories;

use App\Filament\Resources\DocumentCategories\Pages\CreateDocumentCategory;
use App\Filament\Resources\DocumentCategories\Pages\EditDocumentCategory;
use App\Filament\Resources\DocumentCategories\Pages\ListDocumentCategories;
use App\Models\DocumentCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Str;

class DocumentCategoryResource extends Resource
{
    use \LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

    protected static ?string $model = DocumentCategory::class;

    public static function getNavigationLabel(): string
    {
        return __('Resource Categories');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Academic Hub');
    }

    public static function getLabel(): ?string
    {
        return __('Category');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Resource Categories');
    }

    protected static ?int $navigationSort = 2;
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->isAdmin();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Category Details'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('name')
                                ->label(__('Name'))
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->required()
                                ->unique(ignoreRecord: true),
                        ]),
                        Grid::make(2)->components([
                            Select::make('parent_id')
                                ->label(__('Parent Category'))
                                ->relationship('parent', 'name', fn($query) => $query->orderBy('name->en'))
                                ->searchable()
                                ->preload(),
                            Select::make('icon')
                                ->label(__('Icon'))
                                ->options([
                                    'heroicon-o-folder' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-folder style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Folder',
                                    'heroicon-o-document-text' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-document-text style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Documents',
                                    'heroicon-o-book-open' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-book-open style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Academic/Learning',
                                    'heroicon-o-presentation-chart-line' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-presentation-chart-line style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Reports & Stats',
                                    'heroicon-o-briefcase' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-briefcase style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Business & Portfolio',
                                    'heroicon-o-academic-cap' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-academic-cap style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Educational',
                                    'heroicon-o-newspaper' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-newspaper style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' News & Articles',
                                    'heroicon-o-photo' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-photo style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Media/Photos',
                                    'heroicon-o-video-camera' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-video-camera style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Video Content',
                                    'heroicon-o-archive-box' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-archive-box style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle;" />') . ' Archive/History',
                                ])
                                ->allowHtml()
                                ->searchable()
                                ->prefixIcon(fn($state) => $state)
                                ->placeholder(__('Select an icon')),
                        ]),
                        Grid::make(2)->components([
                            TextInput::make('sort_order')
                                ->label(__('Order'))
                                ->numeric()
                                ->default(0),
                            Toggle::make('isActive')
                                ->label(__('Is Active'))
                                ->default(true),
                        ]),
                        Textarea::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(query: fn($query, $direction) => $query->orderBy('name->en', $direction)),
                TextColumn::make('parent.name')
                    ->label(__('Parent Category'))
                    ->badge()
                    ->placeholder(__('Top Level')),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable(),
                TextColumn::make('documents_count')
                    ->counts('documents')
                    ->label(__('Resources')),
                TextColumn::make('sort_order')
                    ->label(__('Order'))
                    ->sortable(),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDocumentCategories::route('/'),
            'create' => CreateDocumentCategory::route('/create'),
            'edit' => EditDocumentCategory::route('/{record}/edit'),
        ];
    }
}
