<?php

namespace App\Filament\Resources\MethodologySteps\Pages;

use App\Filament\Resources\MethodologySteps\MethodologyStepResource;
use App\Models\MethodologyStep;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListMethodologySteps extends ListRecords
{
    use Translatable;

    protected static string $resource = MethodologyStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Action::make('generateFakeMethodology')
                ->label(__('Generate Fake Steps'))
                ->icon('heroicon-m-sparkles')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading(__('Generate fake methodology steps?'))
                ->modalDescription(__('This will create or update the default demo methodology steps used on the website.'))
                ->action(function (): void {
                    foreach (self::fakeMethodologySteps() as $step) {
                        MethodologyStep::updateOrCreate(
                            ['orderIndex' => $step['orderIndex']],
                            $step,
                        );
                    }

                    Notification::make()
                        ->title(__('Fake methodology steps generated'))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }

    protected static function fakeMethodologySteps(): array
    {
        return [
            [
                'icon' => 'lucide-users',
                'title' => ['en' => 'Consultation & Analysis', 'km' => 'ការពិគ្រោះយោបល់ និងការវិភាគ'],
                'description' => ['en' => 'We clarify project goals, review site conditions, and confirm the practical requirements before work begins.', 'km' => 'យើងកំណត់គោលដៅគម្រោង ពិនិត្យលក្ខខណ្ឌទីតាំង និងបញ្ជាក់តម្រូវការជាក់ស្តែងមុនចាប់ផ្តើមការងារ។'],
                'orderIndex' => 1,
                'isActive' => true,
            ],
            [
                'icon' => 'lucide-ruler',
                'title' => ['en' => 'Planning & Design', 'km' => 'ការរៀបចំផែនការ និងរចនា'],
                'description' => ['en' => 'Our team prepares the scope, design direction, timeline, budget baseline, and approval path.', 'km' => 'ក្រុមការងាររៀបចំវិសាលភាព ទិសដៅរចនា កាលវិភាគ ថវិកាមូលដ្ឋាន និងដំណើរការអនុម័ត។'],
                'orderIndex' => 2,
                'isActive' => true,
            ],
            [
                'icon' => 'lucide-hard-hat',
                'title' => ['en' => 'Construction Execution', 'km' => 'ការអនុវត្តសំណង់'],
                'description' => ['en' => 'We coordinate teams, materials, site safety, and daily progress so the project moves according to plan.', 'km' => 'យើងសម្របសម្រួលក្រុមការងារ សម្ភារៈ សុវត្ថិភាពទីតាំង និងវឌ្ឍនភាពប្រចាំថ្ងៃឲ្យគម្រោងដំណើរការតាមផែនការ។'],
                'orderIndex' => 3,
                'isActive' => true,
            ],
            [
                'icon' => 'lucide-shield-check',
                'title' => ['en' => 'Quality Control', 'km' => 'ការត្រួតពិនិត្យគុណភាព'],
                'description' => ['en' => 'Each stage is checked against technical standards, drawings, and client expectations before moving forward.', 'km' => 'រាល់ដំណាក់កាលត្រូវបានត្រួតពិនិត្យតាមស្តង់ដារបច្ចេកទេស គំនូសប្លង់ និងការរំពឹងទុករបស់អតិថិជនមុនបន្តការងារ។'],
                'orderIndex' => 4,
                'isActive' => true,
            ],
            [
                'icon' => 'lucide-check-circle-2',
                'title' => ['en' => 'Handover & Support', 'km' => 'ការប្រគល់ការងារ និងគាំទ្រ'],
                'description' => ['en' => 'We complete documentation, final inspection, handover, and follow-up support for a clean project close.', 'km' => 'យើងបញ្ចប់ឯកសារ ការត្រួតពិនិត្យចុងក្រោយ ការប្រគល់ការងារ និងការគាំទ្របន្តសម្រាប់បិទគម្រោងឲ្យបានរលូន។'],
                'orderIndex' => 5,
                'isActive' => true,
            ],
        ];
    }
}
