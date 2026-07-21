<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCategoryResource\Pages;
use App\Filament\Support\AIHelper;
use App\Filament\Support\FlatRecordDetails;
use App\Models\ProjectCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class ProjectCategoryResource extends Resource
{
    use Translatable;

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

    public static function canDelete(Model $record): bool
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
                                ->label(__('Name').' (English)')
                                ->required()
                                ->live(onBlur: true)
                                ->suffixAction(AIHelper::getTranslateAction('name_en', 'name_km', 'Khmer', 'km', 'en'))
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('name_km')
                                ->label(__('Name').' (Khmer)')
                                ->suffixAction(AIHelper::getTranslateAction('name_km', 'name_en', 'English', 'en', 'km')),
                        ]),
                        TextInput::make('slug')
                            ->label(__('Slug'))
                            ->required()
                            ->unique(ignoreRecord: true),
                        Grid::make(2)->components([
                            Textarea::make('description_en')
                                ->label(__('Description').' (English)')
                                ->hintActions([
                                    AIHelper::getGenerateAction('description_en', 'Project Category Description'),
                                    AIHelper::getTranslateAction('description_en', 'description_km', 'Khmer', 'km', 'en'),
                                ])
                                ->rows(4),
                            Textarea::make('description_km')
                                ->label(__('Description').' (Khmer)')
                                ->hintAction(AIHelper::getTranslateAction('description_km', 'description_en', 'English', 'en', 'km'))
                                ->rows(4),
                        ]),
                    ]),
                Section::make(__('Settings'))
                    ->components([
                        Select::make('parent_id')
                            ->label(__('Parent Category'))
                            ->relationship('parent', 'name', fn ($query) => $query->orderBy('name->en'))
                            ->getOptionLabelFromRecordUsing(
                                fn (ProjectCategory $record): string => $record->localizedName()
                            )
                            ->searchable()
                            ->preload(),
                        Toggle::make('isActive')
                            ->label(__('Is Active'))
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('parent')->withCount('projects'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(query: fn ($query, $direction) => $query->orderBy('name->en', $direction)),
                TextColumn::make('parent.name')
                    ->label(__('Parent Category'))
                    ->badge()
                    ->formatStateUsing(
                        fn (mixed $state, ProjectCategory $record): string => $record->parent?->localizedName()
                            ?: (is_string($state) ? $state : '')
                    )
                    ->placeholder(__('Top Level')),
                TextColumn::make('projects_count')
                    ->counts('projects')
                    ->label(__('Projects')),
                ToggleColumn::make('isActive')
                    ->label(__('Active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make()->schema(fn ($record): array => FlatRecordDetails::schema($record)),
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
