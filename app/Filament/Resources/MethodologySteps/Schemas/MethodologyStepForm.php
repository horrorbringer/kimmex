<?php

namespace App\Filament\Resources\MethodologySteps\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Filament\Support\TranslationHelper;

class MethodologyStepForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->components([
                        TextInput::make('title')
                            ->required()
                            ->suffixAction(TranslationHelper::getAutoTranslateAction('title')),
                        RichEditor::make('description')
                            ->required()
                            ->hintAction(TranslationHelper::getAutoTranslateAction('description'))
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->components([
                                \Filament\Forms\Components\Select::make('icon')
                                    ->label(__('Icon'))
                                    ->options([
                                        'lucide-pen-tool' => \Illuminate\Support\Facades\Blade::render('<x-lucide-pen-tool style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Design & Tool',
                                        'lucide-hammer' => \Illuminate\Support\Facades\Blade::render('<x-lucide-hammer style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Construction & Build',
                                        'lucide-settings' => \Illuminate\Support\Facades\Blade::render('<x-lucide-settings style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Settings & Management',
                                        'lucide-truck' => \Illuminate\Support\Facades\Blade::render('<x-lucide-truck style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Infrastructure & Truck',
                                        'lucide-building' => \Illuminate\Support\Facades\Blade::render('<x-lucide-building style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Commercial Building',
                                        'lucide-hard-hat' => \Illuminate\Support\Facades\Blade::render('<x-lucide-hard-hat style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Safety & Personnel',
                                        'lucide-ruler' => \Illuminate\Support\Facades\Blade::render('<x-lucide-ruler style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Planning & Drafting',
                                        'lucide-factory' => \Illuminate\Support\Facades\Blade::render('<x-lucide-factory style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Industrial Factory',
                                        'lucide-users' => \Illuminate\Support\Facades\Blade::render('<x-lucide-users style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />') . ' Consultation & Users',
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
                                    ->placeholder(__('Select an icon')),
                                TextInput::make('orderIndex')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('isActive')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ])
            ]);
    }
}
