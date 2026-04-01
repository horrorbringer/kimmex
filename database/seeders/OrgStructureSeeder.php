<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\OrgUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrgStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Departments
        $deptsData = [
            ['slug' => 'executive', 'en' => 'Executive Office', 'km' => 'ការិយាល័យនាយកប្រតិបត្តិ'],
            ['slug' => 'management', 'en' => 'General Management', 'km' => 'ការគ្រប់គ្រងទូទៅ'],
            ['slug' => 'technical', 'en' => 'Technical & Design', 'km' => 'បច្ចេកទេស និងការរចនា'],
            ['slug' => 'project-mgmt', 'en' => 'Project Management', 'km' => 'ការគ្រប់គ្រងគម្រោង'],
            ['slug' => 'finance', 'en' => 'Finance & Accounting', 'km' => 'ហិរញ្ញវត្ថុ និងគណនេយ្យ'],
            ['slug' => 'procurement', 'en' => 'Procurement', 'km' => 'លទ្ធកម្ម'],
            ['slug' => 'hr-admin', 'en' => 'HR & Admin', 'km' => 'ធនធានមនុស្ស និងរដ្ឋបាល'],
            ['slug' => 'it-systems', 'en' => 'IT & Systems', 'km' => 'អាយធី និងប្រព័ន្ធ'],
            ['slug' => 'business', 'en' => 'Business & Marketing', 'km' => 'អាជីវកម្ម និងទីផ្សារ'],
            ['slug' => 'construction', 'en' => 'Construction', 'km' => 'ការសាងសង់'],
            ['slug' => 'hseq', 'en' => 'HSEQ & Safety', 'km' => 'សុខភាព សុវត្ថិភាព និងបរិស្ថាន'],
        ];

        $departments = [];
        foreach ($deptsData as $data) {
            $departments[$data['slug']] = Department::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => ['en' => $data['en'], 'km' => $data['km']],
                    'description' => ['en' => "KIMMEX {$data['en']} Department", 'km' => "នាយកដ្ឋាន {$data['km']} របស់ក្រុមហ៊ុន គីម ម៉ិច"],
                ]
            );
        }

        // 2. Create Employees & Org Units Hierarchy
        
        // Level 0: CEO
        $ceoEmp = Employee::updateOrCreate(
            ['name' => 'TOUCH KIM'],
            ['role' => 'CEO', 'phone' => '', 'image' => 'employees/ceo.jpg']
        );
        $ceoUnit = OrgUnit::updateOrCreate(
            ['employeeId' => $ceoEmp->id],
            [
                'title' => ['en' => 'CEO', 'km' => 'នាយកប្រតិបត្តិ (CEO)'],
                'type' => 'EXECUTIVE',
                'departmentId' => $departments['executive']->id,
                'orderIndex' => 1
            ]
        );

        // Level 1: DCEO
        $dceoEmp = Employee::updateOrCreate(
            ['name' => 'RANY SOPHUAK'],
            ['role' => 'DCEO', 'phone' => '017 999 555', 'image' => 'employees/dceo.jpg']
        );
        $dceoUnit = OrgUnit::updateOrCreate(
            ['employeeId' => $dceoEmp->id],
            [
                'title' => ['en' => 'DCEO', 'km' => 'អគ្គនាយករង (DCEO)'],
                'type' => 'EXECUTIVE',
                'parentId' => $ceoUnit->id,
                'departmentId' => $departments['executive']->id,
                'orderIndex' => 1
            ]
        );

        // Level 2: DGM (Management Hub)
        $dgmEmp = Employee::updateOrCreate(
            ['name' => 'TOUCH RUMREAKSAY'],
            ['role' => 'DGM', 'phone' => '012 333 555', 'image' => 'employees/dgm.jpg']
        );
        $dgmUnit = OrgUnit::updateOrCreate(
            ['employeeId' => $dgmEmp->id],
            [
                'title' => ['en' => 'DGM', 'km' => 'អគ្គនាយករង (DGM)'],
                'type' => 'MANAGEMENT',
                'parentId' => $dceoUnit->id,
                'departmentId' => $departments['management']->id,
                'orderIndex' => 1
            ]
        );

        // Level 3: Directors under DGM
        $finDirEmp = Employee::updateOrCreate(
            ['name' => 'JENG VANNAK PH'],
            ['role' => 'Finance Director', 'phone' => '077 777 555']
        );
        $finDirUnit = OrgUnit::updateOrCreate(
            ['employeeId' => $finDirEmp->id],
            [
                'title' => ['en' => 'Finance Director', 'km' => 'នាយកហិរញ្ញវត្ថុ'],
                'type' => 'DIRECTOR',
                'parentId' => $dgmUnit->id,
                'departmentId' => $departments['finance']->id,
                'orderIndex' => 1
            ]
        );

        $scDirEmp = Employee::updateOrCreate(
            ['name' => 'CHHOURN PUTSNA'],
            ['role' => 'Supply Chain Director', 'phone' => '011 333 555']
        );
        $scDirUnit = OrgUnit::updateOrCreate(
            ['employeeId' => $scDirEmp->id],
            [
                'title' => ['en' => 'Supply Chain Director', 'km' => 'នាយកខ្សែសង្វាក់ផ្គត់ផ្គង់'],
                'type' => 'DIRECTOR',
                'parentId' => $dgmUnit->id,
                'departmentId' => $departments['procurement']->id,
                'orderIndex' => 2
            ]
        );

        // Level 4: Managers
        Employee::updateOrCreate(
            ['name' => 'KEO PHALLY'],
            ['role' => 'HR & Liaison Manager', 'phone' => '081 333 555']
        );
        OrgUnit::updateOrCreate(
            ['title->en' => 'HR & Liaison Manager'],
            [
                'title' => ['en' => 'HR & Liaison Manager', 'km' => 'អ្នកគ្រប់គ្រងធនធានមនុស្ស និងទំនាក់ទំនង'],
                'employeeId' => Employee::where('name', 'KEO PHALLY')->first()->id,
                'type' => 'MANAGER',
                'parentId' => $dgmUnit->id,
                'departmentId' => $departments['hr-admin']->id,
                'orderIndex' => 3
            ]
        );

        Employee::updateOrCreate(
            ['name' => 'TY NOREN KEO'],
            ['role' => 'Legal', 'phone' => '012 444 555']
        );
        OrgUnit::updateOrCreate(
            ['title->en' => 'Legal Head'],
            [
                'title' => ['en' => 'Legal Head', 'km' => 'ប្រធានផ្នែកច្បាប់'],
                'employeeId' => Employee::where('name', 'TY NOREN KEO')->first()->id,
                'type' => 'MANAGER',
                'parentId' => $dgmUnit->id,
                'departmentId' => $departments['hr-admin']->id,
                'orderIndex' => 4
            ]
        );

        Employee::updateOrCreate(
            ['name' => 'CHORN VUTHANAK'],
            ['role' => 'Internal Audit', 'phone' => '012 555 555']
        );
        OrgUnit::updateOrCreate(
            ['title->en' => 'Internal Auditor'],
            [
                'title' => ['en' => 'Internal Auditor', 'km' => 'សវនករផ្ទៃក្នុង'],
                'employeeId' => Employee::where('name', 'CHORN VUTHANAK')->first()->id,
                'type' => 'MANAGER',
                'parentId' => $dgmUnit->id,
                'departmentId' => $departments['management']->id,
                'orderIndex' => 5
            ]
        );

        // Level 4: Operations / Site (Under DCEO Hub)
        Employee::updateOrCreate(
            ['name' => 'KIM KEAK'],
            ['role' => 'MEP Operation', 'phone' => '085 333 555']
        );
        OrgUnit::updateOrCreate(
            ['title->en' => 'MEP Operation Manager'],
            [
                'title' => ['en' => 'MEP Operation Manager', 'km' => 'អ្នកគ្រប់គ្រងប្រតិបត្តិការ MEP'],
                'employeeId' => Employee::where('name', 'KIM KEAK')->first()->id,
                'type' => 'MANAGER',
                'parentId' => $dceoUnit->id,
                'departmentId' => $departments['technical']->id,
                'orderIndex' => 10
            ]
        );

        // Sub-managers under Finance
        Employee::updateOrCreate(
            ['name' => 'SEEY SREANA'],
            ['role' => 'Finance Manager', 'phone' => '089 999 555']
        );
        OrgUnit::updateOrCreate(
            ['title->en' => 'Finance Manager'],
            [
                'title' => ['en' => 'Finance Manager', 'km' => 'អ្នកគ្រប់គ្រងហិរញ្ញវត្ថុ'],
                'employeeId' => Employee::where('name', 'SEEY SREANA')->first()->id,
                'type' => 'MANAGER',
                'parentId' => $finDirUnit->id,
                'departmentId' => $departments['finance']->id,
                'orderIndex' => 1
            ]
        );

        Employee::updateOrCreate(
            ['name' => 'HUON KHOA'],
            ['role' => 'Business Unit Manager', 'phone' => '']
        );
        OrgUnit::updateOrCreate(
            ['title->en' => 'Business Unit Manager'],
            [
                'title' => ['en' => 'Business Unit Manager', 'km' => 'អ្នកគ្រប់គ្រងអង្គភាពអាជីវកម្ម'],
                'employeeId' => Employee::where('name', 'HUON KHOA')->first()->id,
                'type' => 'MANAGER',
                'parentId' => $finDirUnit->id,
                'departmentId' => $departments['finance']->id,
                'orderIndex' => 2
            ]
        );
    }
}
