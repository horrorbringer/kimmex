<?php

namespace App\Filament\Resources\MethodologySteps\Schemas;

use App\Filament\Support\AIHelper;
use App\Filament\Support\TranslationHelper;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;

class MethodologyStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                            ->hintAction(AIHelper::getImproveAction('title', 'Improve this methodology step title so it is concise and action-oriented.')),
                        RichEditor::make('description')->resizableImages()
                            ->label(__('Description'))
                            ->required()
                            ->hintActions([
                                AIHelper::getGenerateAction('description', 'Methodology Step Description'),
                                AIHelper::getImproveAction('description', 'Improve this methodology step description for a construction company workflow.'),
                                TranslationHelper::getAutoTranslateAction('description'),
                            ])
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->components([
                                Select::make('icon')
                                    ->label(__('Icon'))
                                    ->options([
                                        'lucide-pen-tool' => Blade::render('<x-lucide-pen-tool style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Design & Tool',
                                        'lucide-hammer' => Blade::render('<x-lucide-hammer style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Construction & Build',
                                        'lucide-settings' => Blade::render('<x-lucide-settings style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Settings & Management',
                                        'lucide-truck' => Blade::render('<x-lucide-truck style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Infrastructure & Truck',
                                        'lucide-building' => Blade::render('<x-lucide-building style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Commercial Building',
                                        'lucide-hard-hat' => Blade::render('<x-lucide-hard-hat style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Safety & Personnel',
                                        'lucide-ruler' => Blade::render('<x-lucide-ruler style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Planning & Drafting',
                                        'lucide-factory' => Blade::render('<x-lucide-factory style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Industrial Factory',
                                        'lucide-users' => Blade::render('<x-lucide-users style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Consultation & Users',
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
                                    ->placeholder(__('Select an icon')),
                                TextInput::make('orderIndex')
                                    ->label(__('Order Index'))
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('isActive')
                                    ->label(__('Is Active'))
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }
}
