<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use App\Filament\Support\TranslationHelper;
use Filament\Schemas\Schema;
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
                                ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label(__('Slug'))
                                ->required()
                                ->unique(ignoreRecord: true),
                            \Filament\Forms\Components\Select::make('icon')
                                ->label(__('Icon'))
                                ->options([
                                    'lucide-pen-tool' => \Illuminate\Support\Facades\Blade::render('<x-lucide-pen-tool style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Design & Tool',
                                    'lucide-hammer' => \Illuminate\Support\Facades\Blade::render('<x-lucide-hammer style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Construction & Build',
                                    'lucide-settings' => \Illuminate\Support\Facades\Blade::render('<x-lucide-settings style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Settings & Management',
                                    'lucide-truck' => \Illuminate\Support\Facades\Blade::render('<x-lucide-truck style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Infrastructure & Truck',
                                    'lucide-building' => \Illuminate\Support\Facades\Blade::render('<x-lucide-building style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Commercial Building',
                                    'lucide-home' => \Illuminate\Support\Facades\Blade::render('<x-lucide-home style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Residential Home',
                                    'lucide-hard-hat' => \Illuminate\Support\Facades\Blade::render('<x-lucide-hard-hat style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Safety & Personnel',
                                    'lucide-ruler' => \Illuminate\Support\Facades\Blade::render('<x-lucide-ruler style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Planning & Drafting',
                                    'lucide-factory' => \Illuminate\Support\Facades\Blade::render('<x-lucide-factory style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Industrial Factory',
                                    'lucide-users' => \Illuminate\Support\Facades\Blade::render('<x-lucide-users style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Consultation & Users',
                                    'lucide-layout-dashboard' => \Illuminate\Support\Facades\Blade::render('<x-lucide-layout-dashboard style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Project Board',
                                    'lucide-check-circle-2' => \Illuminate\Support\Facades\Blade::render('<x-lucide-check-circle-2 style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Success & Reporting',
                                    'lucide-globe' => \Illuminate\Support\Facades\Blade::render('<x-lucide-globe style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' International/Web',
                                    'lucide-briefcase' => \Illuminate\Support\Facades\Blade::render('<x-lucide-briefcase style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Portfolio/Business',
                                    'lucide-shield-check' => \Illuminate\Support\Facades\Blade::render('<x-lucide-shield-check style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Quality & Security',
                                    'lucide-clock' => \Illuminate\Support\Facades\Blade::render('<x-lucide-clock style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Timely Delivery',
                                    'lucide-zap' => \Illuminate\Support\Facades\Blade::render('<x-lucide-zap style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Fast & Innovative',
                                ])
                                ->allowHtml()
                                ->searchable()
                                ->prefixIcon(fn($state) => $state)
                                ->placeholder(__('Select an icon'))
                                ->columnSpan(2),
                        ]),
                    ]),

                Section::make(__('Content Details'))
                    ->components([
                        Grid::make(1)->components([
                            Textarea::make('summary')
                                ->label(__('Summary'))
                                ->hintAction(TranslationHelper::getAutoTranslateAction('summary'))
                                ->maxLength(1000),
                            RichEditor::make('description')
                                ->label(__('Description')),
                        ]),
                    ]),

                Section::make(__('Media & Features'))
                    ->components([
                        FileUpload::make('image')
                            ->label(__('Image'))
                            ->image()
                            ->disk('public')
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
