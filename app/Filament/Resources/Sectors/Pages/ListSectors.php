<?php

namespace App\Filament\Resources\Sectors\Pages;

use App\Filament\Resources\Sectors\SectorResource;
use App\Models\Sector;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;

class ListSectors extends ListRecords
{
    use Translatable;

    protected static string $resource = SectorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            Action::make('generateDefaultSectors')
                ->label(__('Generate Default Sectors'))
                ->icon('heroicon-m-sparkles')
                ->color('gray')
                ->visible(fn (): bool => Sector::query()->count() === 0)
                ->requiresConfirmation()
                ->modalHeading(__('Generate default sectors?'))
                ->modalDescription(__('This will create or update the default industry sectors displayed on the Services page.'))
                ->action(function (): void {
                    foreach (self::defaultSectors() as $sector) {
                        Sector::updateOrCreate(
                            ['orderIndex' => $sector['orderIndex']],
                            $sector,
                        );
                    }

                    Notification::make()
                        ->title(__('Default sectors generated successfully'))
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }

    public static function defaultSectors(): array
    {
        return [
            [
                'title' => ['en' => 'Government', 'km' => 'រដ្ឋាភិបាល'],
                'description' => [
                    'en' => 'Government complexes, municipal infrastructure, and institutional civic projects.',
                    'km' => 'អគាររដ្ឋបាលរដ្ឋាភិបាល ហេដ្ឋារចនាសម្ព័ន្ធក្រុង និងគម្រោងស្ថាប័នរដ្ឋ។',
                ],
                'image' => '/images/webp/projects/Thumbnail-1.webp',
                'icon' => 'lucide-landmark',
                'orderIndex' => 1,
                'isActive' => true,
            ],
            [
                'title' => ['en' => 'Education', 'km' => 'អប់រំ'],
                'description' => [
                    'en' => 'University campuses, vocational training centers, and school facilities.',
                    'km' => 'បរិវេណសាកលវិទ្យាល័យ មជ្ឈមណ្ឌលបណ្តុះបណ្តាលវិជ្ជាជីវៈ និងសាលារៀន។',
                ],
                'image' => '/images/webp/projects/Thumbnail-2.webp',
                'icon' => 'lucide-graduation-cap',
                'orderIndex' => 2,
                'isActive' => true,
            ],
            [
                'title' => ['en' => 'Commercial', 'km' => 'ពាណិជ្ជកម្ម'],
                'description' => [
                    'en' => 'High-rise office towers, modern retail plazas, and mixed-use commercial centers.',
                    'km' => 'អគារការិយាល័យពាណិជ្ជកម្ម មជ្ឈមណ្ឌលលក់រាយ និងអគារពហុមុខងារទំនើប។',
                ],
                'image' => '/images/webp/projects/Thumbnail-3.webp',
                'icon' => 'lucide-building',
                'orderIndex' => 3,
                'isActive' => true,
            ],
            [
                'title' => ['en' => 'Infrastructure', 'km' => 'ហេដ្ឋារចនាសម្ព័ន្ធ'],
                'description' => [
                    'en' => 'Transportation routes, civil engineering networks, and industrial development.',
                    'km' => 'ផ្លូវគមនាគមន៍ បណ្តាញវិស្វកម្មស៊ីវិល និងការអភិវឌ្ឍឧស្សាហកម្ម។',
                ],
                'image' => '/images/webp/projects/Thumbnail-6.webp',
                'icon' => 'lucide-route',
                'orderIndex' => 4,
                'isActive' => true,
            ],
        ];
    }
}
