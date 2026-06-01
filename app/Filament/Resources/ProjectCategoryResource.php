<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCategoryResource\Pages;
use App\Filament\Support\AIHelper;
use App\Models\ProjectCategory;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ProjectCategoryResource extends Resource
{
    use \LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

    protected static ?string $model = ProjectCategory::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';
    public static function getNavigationLabel(): string
    {
        return __('Project Categories');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Portfolio');
    }

    public static function getLabel(): ?string
    {
        return __('Category');
    }

    public static function getPluralLabel(): ?string
    {
        return __('Project Categories');
    }

    protected static ?int $navigationSort = 2;

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
                            TextInput::make('name_en')
                                ->label(__('Name') . ' (English)')
                                ->required()
                                ->live(onBlur: true)
                                ->suffixAction(AIHelper::getTranslateAction('name_en', 'name_km', 'Khmer', 'km', 'en'))
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('name_km')
                                ->label(__('Name') . ' (Khmer)')
                                ->suffixAction(AIHelper::getTranslateAction('name_km', 'name_en', 'English', 'en', 'km')),
                        ]),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->unique(ignoreRecord: true),
                        Grid::make(2)->components([
                            Textarea::make('description_en')
                                ->label(__('Description') . ' (English)')
                                ->hintActions([
                                    AIHelper::getGenerateAction('description_en', 'Project Category Description'),
                                    AIHelper::getTranslateAction('description_en', 'description_km', 'Khmer', 'km', 'en'),
                                ])
                                ->rows(4),
                            Textarea::make('description_km')
                                ->label(__('Description') . ' (Khmer)')
                                ->hintAction(AIHelper::getTranslateAction('description_km', 'description_en', 'English', 'en', 'km'))
                                ->rows(4),
                        ]),
                    ]),
                Section::make(__('Settings'))
                    ->components([
                        Select::make('parent_id')
                            ->label(__('Parent Category'))
                            ->relationship('parent', 'name', fn($query) => $query->orderBy('name->en'))
                            ->getOptionLabelFromRecordUsing(
                                fn(ProjectCategory $record): string => $record->localizedName()
                            )
                            ->searchable()
                            ->preload(),
                        \Filament\Forms\Components\Toggle::make('isActive')
                            ->label(__('Is Active'))
                            ->default(true),
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
                    ->formatStateUsing(
                        fn (mixed $state, ProjectCategory $record): string => $record->parent?->localizedName()
                            ?: (is_string($state) ? $state : '')
                    )
                    ->placeholder(__('Top Level')),
                TextColumn::make('slug')
                    ->label(__('Slug'))
                    ->searchable(),
                TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label(__('Projects')),
                \Filament\Tables\Columns\ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectCategories::route('/'),
            'create' => Pages\CreateProjectCategory::route('/create'),
            'edit' => Pages\EditProjectCategory::route('/{record}/edit'),
        ];
    }
}
