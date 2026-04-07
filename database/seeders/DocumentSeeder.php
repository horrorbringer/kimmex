<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\DocumentCategory;
use App\Models\Document;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define standard categories
        $categoriesData = [
            'Engineering Standards' => 'Technical specifications and construction methodologies.',
            'Safety Manuals' => 'Policies and protocols for site safety and emergency response.',
            'Case Studies' => 'In-depth reviews and outcomes of completed projects.',
            'Corporate' => 'Company-wide updates, organizational charts, and annual reports.',
            'Technical' => 'MEP design guidelines, materials testing, and quality control.',
            'Compliance & Legal' => 'Regulatory documents, zoning laws, and contract templates.',
        ];

        $categories = [];
        $order = 1;

        foreach ($categoriesData as $name => $desc) {
            $categories[$name] = DocumentCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => [
                        'en' => $name,
                        'km' => $name . ' (Khmer)'
                    ],
                    'description' => [
                        'en' => $desc,
                        'km' => $desc . ' (Khmer)'
                    ],
                    'sort_order' => $order++
                ]
            );
        }

        // Dummy Documents Data
        $documents = [
            [
                'title' => 'Structural Integrity Guidelines 2026',
                'desc' => 'Comprehensive framework detailing the latest loadbearing methodologies and material stress testing protocols for high-rise commercial structures.',
                'cat' => 'Engineering Standards',
                'type' => 'PDF',
                'size' => '18.4 MB',
                'featured' => true,
            ],
            [
                'title' => 'MEP Systems Integration Handbook',
                'desc' => 'Standard operating procedures for implementing Mechanical, Electrical, and Plumbing systems efficiently without structural interference.',
                'cat' => 'Technical',
                'type' => 'PDF',
                'size' => '8.1 MB',
                'featured' => false,
            ],
            [
                'title' => 'Site Safety & Risk Management Protocol',
                'desc' => 'Updated safety requirements for active worksites including high-visibility PPE, harness regulations, and evacuation muster points.',
                'cat' => 'Safety Manuals',
                'type' => 'PDF',
                'size' => '4.2 MB',
                'featured' => true,
            ],
            [
                'title' => 'Ministry of Infrastructure Complex: Case Study',
                'desc' => 'A detailed post-mortem on the delivery of the 15-story government complex, detailing challenges overcome in deep foundation piling.',
                'cat' => 'Case Studies',
                'type' => 'PDF',
                'size' => '12.5 MB',
                'featured' => false,
            ],
            [
                'title' => 'Kimmex Annual Corporate Report 2025',
                'desc' => 'Financial disclosures, market expansion milestones, and strategic growth outlines for the preceding fiscal year.',
                'cat' => 'Corporate',
                'type' => 'PDF',
                'size' => '3.5 MB',
                'featured' => false,
            ],
            [
                'title' => 'Sustainable Green Building Roadmap',
                'desc' => 'Exploring eco-friendly materials, energy-efficient designs, and achieving LEED certifications in the Southeast Asian climate.',
                'cat' => 'Engineering Standards',
                'type' => 'PDF',
                'size' => '6.8 MB',
                'featured' => true,
            ],
            [
                'title' => 'Subcontractor Legal Compliance Packet',
                'desc' => 'Essential legal forms, compliance waivers, and non-disclosure agreements mandatory for all external vendor procurement.',
                'cat' => 'Compliance & Legal',
                'type' => 'DOCX',
                'size' => '1.2 MB',
                'featured' => false,
            ],
            [
                'title' => 'Emergency Response Protocol V3',
                'desc' => 'Step-by-step action plans for medical emergencies, fires, and extreme weather occurrences on construction sites.',
                'cat' => 'Safety Manuals',
                'type' => 'PDF',
                'size' => '2.5 MB',
                'featured' => false,
            ]
        ];

        foreach ($documents as $doc) {
            $cat = $categories[$doc['cat']] ?? null;

            if ($cat) {
                Document::firstOrCreate(
                    ['slug' => Str::slug($doc['title'])],
                    [
                        'title' => [
                            'en' => $doc['title'],
                            'km' => $doc['title'] . ' (Khmer)'
                        ],
                        'description' => [
                            'en' => $doc['desc'],
                            'km' => $doc['desc'] . ' (Khmer)'
                        ],
                        'category' => $doc['cat'],
                        'document_category_id' => $cat->id,
                        'fileType' => $doc['type'],
                        'fileSize' => $doc['size'],
                        'fileUrl' => 'documents/dummy-' . Str::slug($doc['title']) . '.pdf',
                        'thumbnailUrl' => null,
                        'isPublic' => true,
                        'is_featured' => $doc['featured'],
                    ]
                );
            }
        }
    }
}
