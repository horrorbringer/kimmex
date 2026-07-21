<?php

namespace Database\Seeders;

use App\Models\NewsArticle;
use Illuminate\Database\Seeder;

class NewsArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'title' => [
                    'en' => 'Green Urban Initiative 2026: A Sustainability Milestone',
                    'km' => 'គំនិតផ្តួចផ្តើមទីក្រុងបៃតង ២០២៦៖ ដំណាក់កាលថ្មីនៃនិរន្តរភាព',
                ],
                'slug' => 'green-initiative',
                'excerpt' => [
                    'en' => 'Kimmex has been officially awarded the 2026 Urban Sustainability Contract, a $50M initiative to transform city transport.',
                    'km' => 'ក្រុមហ៊ុន Kimmex ត្រូវបានផ្តល់កិច្ចសន្យានិរន្តរភាពទីក្រុងឆ្នាំ ២០២៦ ជាផ្លូវការ ដែលជាគម្រោង ៥០លានដុល្លារ ដើម្បីផ្លាស់ប្តូរការដឹកជញ្ជូនក្នុងទីក្រុង។',
                ],
                'content' => [
                    'en' => '
                        <p>In a significant milestone for sustainable development in Southeast Asia, Kimmex Construction & Investment Co., Ltd. has announced its leadership in the <strong>Green Urban Initiative 2026</strong>. This project aims to integrate smart grid technology with carbon-neutral construction materials across major metropolitan routes.</p>
                        
                        <figure class="my-12">
                            <img src="/images/projects/Thumbnail-6.jpg" class="rounded-3xl shadow-2xl w-full" alt="Green Hub Project" />
                            <figcaption class="text-xs text-center text-gray-400 mt-4 uppercase tracking-widest">Architectural Visualization of the 2026 Green Hub</figcaption>
                        </figure>

                        <p>Our team of engineers has spent over 18 months developing a proprietary "Eco-Concrete" blend that reduces CO2 emissions by 40% compared to traditional standards. This innovation is at the heart of the new project.</p>
                        
                        <div class="my-12 aspect-video rounded-3xl overflow-hidden shadow-2xl">
                            <iframe class="w-full h-full" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Project Overview" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>

                        <p>Beyond materials, the initiative also includes a massive reforestation effort within urban centers, integrating living vertical gardens into infrastructure supports.</p>
                    ',
                    'km' => '
                        <p>ក្នុងដំណាក់កាលដ៏សំខាន់សម្រាប់ការអភិវឌ្ឍប្រកបដោយចីរភាពនៅអាស៊ីអាគ្នេយ៍ ក្រុមហ៊ុន Kimmex Construction & Investment Co., Ltd. បានប្រកាសពីភាពជាអ្នកដឹកនាំរបស់ខ្លួនក្នុង <strong>គំនិតផ្តួចផ្តើមទីក្រុងបៃតង ២០២៦</strong>។</p>
                    ',
                ],
                'category' => 'Infrastructure',
                'isFeatured' => true,
                'gallery' => [
                    '/images/projects/Thumbnail-1.jpg',
                    '/images/projects/Thumbnail-2.jpg',
                    '/images/projects/Thumbnail-3.jpg',
                    '/images/projects/Thumbnail-4.jpg',
                ],
                'authorName' => [
                    'en' => 'Dr. Sok Chea',
                    'km' => 'បណ្ឌិត សុខ ជា',
                ],
                'readTime' => [
                    'en' => '5 min read',
                    'km' => '៥ នាទី',
                ],
            ],
            [
                'title' => [
                    'en' => 'Kimmex Achieves 1 Million Safe Man-Hours on SkyTower Project',
                    'km' => 'ក្រុមហ៊ុន Kimmex សម្រេចបាន ១ លានម៉ោងការងារប្រកបដោយសុវត្ថិភាពលើគម្រោង SkyTower',
                ],
                'slug' => 'safety-milestone-skytower',
                'excerpt' => [
                    'en' => 'A testament to our unwavering commitment to employee well-being and rigorous safety protocols.',
                    'km' => 'សក្ខីភាពមួយចំពោះការប្តេជ្ញាចិត្តមិនងាករេរបស់យើងចំពោះសុខុមាលភាពបុគ្គលិក និងពិធីសារសុវត្ថិភាពដ៏តឹងរឹង។',
                ],
                'content' => [
                    'en' => '
                        <p>Safety is the cornerstone of every Kimmex project. We are proud to announce that the SkyTower development team has surpassed <strong>one million man-hours</strong> without a single lost-time injury (LTI).</p>
                    ',
                    'km' => '<p>សុវត្ថិភាពគឺជាមូលដ្ឋានគ្រឹះនៃរាល់គម្រោងរបស់ Kimmex។</p>',
                ],
                'category' => 'Safety',
                'isFeatured' => false,
                'gallery' => [
                    '/images/projects/Thumbnail-5.jpg',
                    '/images/projects/Thumbnail-7.jpg',
                ],
                'authorName' => [
                    'en' => 'HSE Dept',
                    'km' => 'ផ្នែកសុវត្ថិភាព',
                ],
                'readTime' => [
                    'en' => '3 min read',
                    'km' => '៣ នាទី',
                ],
            ],
        ];

        foreach ($articles as $articleData) {
            NewsArticle::updateOrCreate(
                ['slug' => $articleData['slug']],
                [
                    'title' => $articleData['title'],
                    'excerpt' => $articleData['excerpt'],
                    'content' => $articleData['content'],
                    'category' => $articleData['category'],
                    'isFeatured' => $articleData['isFeatured'],
                    'gallery' => $articleData['gallery'] ?? null,
                    'authorName' => $articleData['authorName'],
                    'readTime' => $articleData['readTime'],
                    'publishedAt' => now(),
                    'coverImage' => null,
                ]
            );
        }
    }
}
