<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('job_postings')
            ->where('slug', 'senior-c-developer')
            ->update([
                'requirements->km' => '<ul><li>ការអប់រំ៖ បរិញ្ញាបត្រ ឬអនុបណ្ឌិតផ្នែកវិទ្យាសាស្ត្រកុំព្យូទ័រ វិស្វកម្មកម្មវិធី ឬជំនាញពាក់ព័ន្ធ។</li><li>បទពិសោធន៍៖ មានបទពិសោធន៍វិជ្ជាជីវៈយ៉ាងតិច ៥–៧ ឆ្នាំក្នុងការអភិវឌ្ឍ C# និង .NET និងមានបទពិសោធន៍ដឹកនាំគម្រោងកម្មវិធី។</li><li>ជំនាញបច្ចេកទេស៖ ចំណេះដឹងជ្រៅជ្រះអំពី .NET Core, ASP.NET MVC, Entity Framework និងស្ថាបត្យកម្ម RESTful API។</li><li>ជំនាញមូលដ្ឋានទិន្នន័យ៖ ជំនាញកម្រិតខ្ពស់នៅក្នុង MS SQL Server រួមទាំងការលៃតម្រូវការអនុវត្ត ការបង្កើតលិបិក្រមស្មុគស្មាញ និងនីតិវិធីដែលបានរក្សាទុក។</li><li>ជំនាញទន់៖ មានសមត្ថភាពដឹកនាំ ជំនាញដោះស្រាយបញ្ហាល្អ និងអាចពន្យល់គំនិតបច្ចេកទេសស្មុគស្មាញទៅកាន់អ្នកពាក់ព័ន្ធបានច្បាស់លាស់។</li><li>ថ្ងៃ និងម៉ោងធ្វើការ៖ ចន្ទ ដល់ សុក្រ ម៉ោង ៨:០០ ព្រឹក ដល់ ១២:០០ ថ្ងៃត្រង់ និងម៉ោង ១:០០ រសៀល ដល់ ៥:៣០ ល្ងាច។</li><li>ទីកន្លែងធ្វើការ៖ ការិយាល័យកណ្តាល បុរី ប៉េង ហួត នាយកដ្ឋាន IT។</li><li>ទំនាក់ទំនងការងារ៖ រាយការណ៍ផ្ទាល់ទៅកាន់អ្នកគ្រប់គ្រងផ្នែក IT។</li></ul>',
            ]);
    }

    public function down(): void
    {
        // This data repair is intentionally irreversible because the prior value was corrupted.
    }
};
