<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\AIHelper;
use App\Filament\Support\TranslationHelper;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('General Information'))
                    ->components([
                        Grid::make(2)->components([
                            TextInput::make('title')
                                ->label(__('Title'))
                                ->required()
                                ->live(onBlur: true)
                                ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->helperText(__('Auto-generated. Click ✏️ to edit manually.'))
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->disabled(fn ($get) => ! $get('_slug_manual'))
                                ->dehydrated()
                                ->suffixAction(
                                    Action::make('toggleSlugManual')
                                        ->icon(fn ($get) => $get('_slug_manual') ? 'heroicon-o-lock-open' : 'heroicon-o-pencil-square')
                                        ->tooltip(fn ($get) => $get('_slug_manual') ? __('Lock (auto-generate)') : __('Edit manually'))
                                        ->action(function (Set $set, $get) {
                                            $set('_slug_manual', ! $get('_slug_manual'));
                                        })
                                ),
                            Hidden::make('_slug_manual')->default(false)->dehydrated(false),
                            Select::make('icon')
                                ->label(__('Icon'))
                                ->options([
                                    'lucide-pen-tool' => Blade::render('<x-lucide-pen-tool style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Design & Tool',
                                    'lucide-hammer' => Blade::render('<x-lucide-hammer style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Construction & Build',
                                    'lucide-settings' => Blade::render('<x-lucide-settings style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Settings & Management',
                                    'lucide-truck' => Blade::render('<x-lucide-truck style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Infrastructure & Truck',
                                    'lucide-building' => Blade::render('<x-lucide-building style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Commercial Building',
                                    'lucide-home' => Blade::render('<x-lucide-home style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Residential Home',
                                    'lucide-hard-hat' => Blade::render('<x-lucide-hard-hat style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Safety & Personnel',
                                    'lucide-ruler' => Blade::render('<x-lucide-ruler style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Planning & Drafting',
                                    'lucide-factory' => Blade::render('<x-lucide-factory style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Industrial Factory',
                                    'lucide-users' => Blade::render('<x-lucide-users style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Consultation & Users',
                                    'lucide-layout-dashboard' => Blade::render('<x-lucide-layout-dashboard style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Project Board',
                                    'lucide-check-circle-2' => Blade::render('<x-lucide-check-circle-2 style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Success & Reporting',
                                    'lucide-globe' => Blade::render('<x-lucide-globe style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' International/Web',
                                    'lucide-briefcase' => Blade::render('<x-lucide-briefcase style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Portfolio/Business',
                                    'lucide-shield-check' => Blade::render('<x-lucide-shield-check style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Quality & Security',
                                    'lucide-clock' => Blade::render('<x-lucide-clock style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Timely Delivery',
                                    'lucide-zap' => Blade::render('<x-lucide-zap style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Fast & Innovative',
                                ])
                                ->allowHtml()
                                ->searchable()
                                ->prefixIcon(fn ($state) => $state)
                                ->placeholder(__('Select an icon'))
                                ->columnSpan(2),
                        ]),
                    ]),

                Section::make(__('Content Details'))
                    ->components([
                        Grid::make(1)->components([
                            Textarea::make('summary')
                                ->label(__('Summary'))
                                ->hintActions([
                                    AIHelper::getImproveAction('summary'),
                                    TranslationHelper::getAutoTranslateAction('summary'),
                                ])
                                ->maxLength(1000),
                            RichEditor::make('description')->resizableImages()
                                ->label(__('Description'))
                                ->toolbarButtons([
                                    ['bold', 'italic', 'underline', 'strike', 'link'],
                                    [ToolbarButtonGroup::make('Heading', ['h2', 'h3', 'h4'])->textualButtons()],
                                    [ToolbarButtonGroup::make('Alignment', ['alignStart', 'alignCenter', 'alignEnd', 'alignJustify'])],
                                    ['blockquote', 'bulletList', 'orderedList', 'table'],
                                    ['attachFiles'],
                                    ['undo', 'redo'],
                                ])
                                ->fileAttachmentsDisk(config('filesystems.public_uploads_disk'))
                                ->fileAttachmentsVisibility('public')
                                ->hintActions([
                                    AIHelper::getGenerateAction('description', 'Service Description'),
                                    AIHelper::getImproveAction('description'),
                                ]),
                        ]),
                    ]),

                Section::make(__('Media & Features'))
                    ->components([
                        FileUpload::make('image')
                            ->label(__('Image'))
                            ->image()
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->directory('services')
                            ->visibility('public')
                            ->columnSpanFull(),

                        Repeater::make('features')
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Name'))
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make(__('Settings'))
                    ->components([
                        Grid::make(2)->components([
                            Toggle::make('isActive')
                                ->label(__('Is Active'))
                                ->default(true),
                            TextInput::make('orderIndex')
                                ->label(__('Order'))
                                ->numeric()
                                ->default(0),
                        ]),
                    ]),
            ]);
    }
}
