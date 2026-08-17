<?php

namespace App\Filament\Resources\Sectors\Schemas;

use App\Filament\Support\AIHelper;
use App\Filament\Support\TranslationHelper;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;

class SectorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('Sector Information'))
                    ->columns(2)
                    ->columnSpanFull()
                    ->components([
                        TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->suffixAction(TranslationHelper::getAutoTranslateAction('title'))
                            ->hintAction(AIHelper::getImproveAction('title', 'Improve this industry/sector title for a construction engineering company.'))
                            ->columnSpan(1),

                        Select::make('icon')
                            ->label(__('Icon'))
                            ->options([
                                'lucide-landmark' => Blade::render('<x-lucide-landmark style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Landmark / Government',
                                'lucide-graduation-cap' => Blade::render('<x-lucide-graduation-cap style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Education / School',
                                'lucide-building' => Blade::render('<x-lucide-building style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Commercial / Office',
                                'lucide-route' => Blade::render('<x-lucide-route style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Route / Infrastructure',
                                'lucide-factory' => Blade::render('<x-lucide-factory style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Factory / Industrial',
                                'lucide-hospital' => Blade::render('<x-lucide-hospital style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Hospital / Healthcare',
                                'lucide-home' => Blade::render('<x-lucide-home style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Residential / Housing',
                                'lucide-plane' => Blade::render('<x-lucide-plane style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Aviation / Airport',
                                'lucide-warehouse' => Blade::render('<x-lucide-warehouse style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Warehouse / Logistics',
                                'lucide-hotel' => Blade::render('<x-lucide-hotel style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Hospitality / Hotel',
                                'lucide-hammer' => Blade::render('<x-lucide-hammer style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Civil Engineering',
                                'lucide-zap' => Blade::render('<x-lucide-zap style="width: 18px; height: 18px; display: inline-block; margin-right: 8px; vertical-align: middle; color: #0F172A;" />').' Energy / Power',
                            ])
                            ->allowHtml()
                            ->searchable()
                            ->placeholder(__('Select an icon'))
                            ->default('lucide-building')
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->label(__('Description'))
                            ->rows(3)
                            ->hintActions([
                                AIHelper::getGenerateAction('description', 'Sector Description for Construction & Engineering'),
                                AIHelper::getImproveAction('description', 'Improve this industry sector description.'),
                                TranslationHelper::getAutoTranslateAction('description'),
                            ])
                            ->columnSpanFull(),

                        TextInput::make('orderIndex')
                            ->label(__('Order Index'))
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->columnSpan(1),

                        Toggle::make('isActive')
                            ->label(__('Is Active'))
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('Enable to display this sector on the public Services page.'))
                            ->default(true)
                            ->inline(false)
                            ->columnSpan(1),

                        FileUpload::make('image')
                            ->label(__('Cover Image'))
                            ->image()
                            ->disk(config('filesystems.public_uploads_disk'))
                            ->directory('sectors')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
