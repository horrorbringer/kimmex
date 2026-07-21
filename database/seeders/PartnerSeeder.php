<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            'MOI', 'MEF', 'EDC', 'NSSF', 'NCE', 'GCDE', 'ACU', 'CCRH', 'NTTI', 'RUPP', 'ITC', 'NTTI-SR', 'Stadium',
        ];

        // Prevent constraint violations if logoUrl is required
        Partner::truncate(); // Let's truncate to clean up existing before seeding fresh

        foreach ($partners as $index => $partner) {
            Partner::create([
                'name' => [
                    'en' => $partner,
                    'km' => $partner,
                ],
                'logoUrl' => 'partners/placeholder.png',
                'type' => 'Government',
                'orderIndex' => $index + 1,
            ]);
        }
    }
}
