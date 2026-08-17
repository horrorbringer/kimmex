<?php

namespace Database\Factories;

use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sector>
 */
class SectorFactory extends Factory
{
    protected $model = Sector::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => [
                'en' => fake()->words(2, true),
                'km' => 'វិស័យ '.fake()->word(),
            ],
            'description' => [
                'en' => fake()->sentence(),
                'km' => fake()->sentence(),
            ],
            'icon' => fake()->randomElement(['lucide-landmark', 'lucide-graduation-cap', 'lucide-building', 'lucide-route', 'lucide-factory', 'lucide-hospital']),
            'image' => '/images/webp/projects/Thumbnail-1.webp',
            'orderIndex' => fake()->numberBetween(1, 10),
            'isActive' => true,
        ];
    }
}
