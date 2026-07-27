<?php

namespace App\Filament\Imports;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\ProjectCategory;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProjectImporter extends Importer
{
    protected static ?string $model = Project::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->label('Project name')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('Cambodia Gambling Management Commission Building')
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->setTranslation('title', 'en', trim($state));
                }),
            ImportColumn::make('slug')
                ->label('Website address')
                ->requiredMapping()
                ->rules(['required', 'string', 'max:255'])
                ->example('cambodia-gambling-management-commission-building')
                ->castStateUsing(fn (mixed $state): string => Str::slug((string) $state))
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->slug = $state;
                }),
            ImportColumn::make('category')
                ->label('Category')
                ->requiredMapping()
                ->rules([
                    'required',
                    'string',
                    'max:255',
                    fn (string $attribute, mixed $value, \Closure $fail): mixed => static::findCategory((string) $value)
                        ? null
                        : $fail('The selected category does not exist. Use its slug or English name.'),
                ])
                ->helperText('Use an existing category slug or English category name.')
                ->example('government')
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $category = static::findCategory($state);

                    if (! $category) {
                        throw new RowImportFailedException('The selected category does not exist.');
                    }

                    $record->projectCategory()->associate($category);
                    $record->category = $category->getTranslation('name', 'en', false) ?: $category->slug;
                }),
            ImportColumn::make('status')
                ->label('Status')
                ->requiredMapping()
                ->rules(['required', Rule::in(array_column(ProjectStatus::cases(), 'value'))])
                ->example(ProjectStatus::COMPLETED->value)
                ->castStateUsing(fn (mixed $state): string => Str::upper(trim((string) $state)))
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->status = ProjectStatus::from($state);
                }),
            ImportColumn::make('location')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Phnom Penh, Cambodia')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->setTranslation('location', 'en', trim($state));
                }),
            ImportColumn::make('client')
                ->label('Client / owner')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Ministry of Economy and Finance')
                ->ignoreBlankState(),
            ImportColumn::make('timeline')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Jan 2024 - Dec 2025')
                ->ignoreBlankState(),
            ImportColumn::make('scale')
                ->label('Built area / scale')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('8,087 m² · 17 floors')
                ->ignoreBlankState(),
            ImportColumn::make('completion_date')
                ->label('Completion date')
                ->rules(['nullable', 'date'])
                ->example('2025-12-31')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->completionDate = $state;
                }),
            ImportColumn::make('hero_image_url')
                ->label('Hero image URL')
                ->rules(['nullable', 'url', 'max:2048'])
                ->example('https://res.cloudinary.com/example/image/upload/projects/hero/project.webp')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->heroImage = trim($state);
                }),
            ImportColumn::make('description')
                ->label('Short introduction')
                ->rules(['nullable', 'string'])
                ->example('A concise overview of the project and its public value.')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->setTranslation('description', 'en', trim($state));
                }),
            ImportColumn::make('background')
                ->label('Background')
                ->rules(['nullable', 'string'])
                ->example('The context, need, and public purpose behind the project.')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->setTranslation('background', 'en', trim($state));
                }),
            ImportColumn::make('objectives')
                ->label('Objectives')
                ->rules(['nullable', 'string'])
                ->helperText('Use line breaks for separate objectives.')
                ->example("Improve public access\nCreate a durable facility\nSupport future growth")
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->setTranslation('objectives', 'en', trim($state));
                }),
            ImportColumn::make('scope_of_work')
                ->label('Scope of work')
                ->rules(['nullable', 'string'])
                ->helperText('Use line breaks for separate responsibilities.')
                ->example("Architectural design\nStructural engineering\nMEP coordination")
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->setTranslation('scopeContributions', 'en', trim($state));
                }),
            ImportColumn::make('design_concept')
                ->label('Design concept')
                ->rules(['nullable', 'string'])
                ->example('A concise description of the architectural and functional design approach.')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->setTranslation('designConcept', 'en', trim($state));
                }),
            ImportColumn::make('engineering_notes')
                ->label('Engineering notes')
                ->rules(['nullable', 'string'])
                ->example('Optional technical challenges, constraints, and solutions.')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, string $state): void {
                    $record->setTranslation('engineeringNarrative', 'en', trim($state));
                }),
            ImportColumn::make('is_featured')
                ->label('Show on home page')
                ->boolean()
                ->rules(['nullable', 'boolean'])
                ->example('0')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, bool $state): void {
                    $record->isFeatured = $state;
                }),
            ImportColumn::make('is_active')
                ->label('Publish project')
                ->boolean()
                ->rules(['nullable', 'boolean'])
                ->example('1')
                ->ignoreBlankState()
                ->fillRecordUsing(function (Project $record, bool $state): void {
                    $record->isActive = $state;
                }),
        ];
    }

    public function resolveRecord(): Project
    {
        return Project::query()->firstOrNew([
            'slug' => $this->data['slug'],
        ]);
    }

    protected function beforeSave(): void
    {
        if (! $this->getImport()->user()->where('role', 'ADMIN')->where('is_active', true)->exists()) {
            throw new RowImportFailedException('Only active administrators can import projects.');
        }
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return 'Project import complete';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = Number::format($import->successful_rows).' '.str('project')->plural($import->successful_rows).' created or updated.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    private static function findCategory(string $value): ?ProjectCategory
    {
        $value = trim($value);

        return ProjectCategory::query()
            ->where('slug', Str::slug($value))
            ->orWhere('name->en', $value)
            ->first();
    }
}
