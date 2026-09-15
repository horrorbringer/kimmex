<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Employee;
use App\Models\OrgUnit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrgStructureTemplateService
{
    /**
     * Available template definitions.
     *
     * @return array<string, array{label: string, description: string, count: int}>
     */
    public static function getTemplateOptions(): array
    {
        return [
            'kimmex_corporate' => [
                'label' => 'KIMMEX Corporate Structure (Full Enterprise)',
                'description' => 'Complete 3-tier hierarchy: CEO, DCEO, DGM, 7 Divisions (Technical, Project, Finance, Procurement, HR, IT, BD) and operational managers.',
                'count' => 16,
            ],
            'standard_company' => [
                'label' => 'Standard Business Structure (SME / Corporate)',
                'description' => 'Modern corporate structure: CEO, COO, CFO, CTO, and Department Heads (Operations, Finance, Tech, HR, Sales).',
                'count' => 8,
            ],
            'starter_root' => [
                'label' => 'Starter Hierarchy (CEO + 3 Core Heads)',
                'description' => 'Quick starter setup with CEO and 3 direct executive branches ready for custom expansion.',
                'count' => 4,
            ],
        ];
    }

    /**
     * Apply a specific template.
     */
    public static function applyTemplate(string $templateKey, bool $clearExisting = true): int
    {
        return DB::transaction(function () use ($templateKey, $clearExisting): int {
            if ($clearExisting) {
                OrgUnit::query()->delete();
            }

            $count = match ($templateKey) {
                'standard_company' => static::buildStandardCompanyTemplate(),
                'starter_root' => static::buildStarterRootTemplate(),
                default => static::buildKimmexCorporateTemplate(),
            };

            static::clearCaches();

            return $count;
        });
    }

    /**
     * Template 1: Full KIMMEX Corporate Structure.
     */
    protected static function buildKimmexCorporateTemplate(): int
    {
        // 1. Departments
        $depts = [
            'executive' => Department::updateOrCreate(['slug' => 'executive'], ['name' => ['en' => 'Executive Office', 'km' => 'ការិយាល័យនាយកប្រតិបត្តិ']]),
            'management' => Department::updateOrCreate(['slug' => 'management'], ['name' => ['en' => 'General Management', 'km' => 'ការគ្រប់គ្រងទូទៅ']]),
            'technical' => Department::updateOrCreate(['slug' => 'technical'], ['name' => ['en' => 'Technical & Design', 'km' => 'បច្ចេកទេស និងការរចនា']]),
            'project-mgmt' => Department::updateOrCreate(['slug' => 'project-mgmt'], ['name' => ['en' => 'Project Management', 'km' => 'ការគ្រប់គ្រងគម្រោង']]),
            'finance' => Department::updateOrCreate(['slug' => 'finance'], ['name' => ['en' => 'Finance & Accounting', 'km' => 'ហិរញ្ញវត្ថុ និងគណនេយ្យ']]),
            'procurement' => Department::updateOrCreate(['slug' => 'procurement'], ['name' => ['en' => 'Procurement', 'km' => 'លទ្ធកម្ម']]),
            'hr-admin' => Department::updateOrCreate(['slug' => 'hr-admin'], ['name' => ['en' => 'HR & Admin', 'km' => 'ធនធានមនុស្ស និងរដ្ឋបាល']]),
            'it-systems' => Department::updateOrCreate(['slug' => 'it-systems'], ['name' => ['en' => 'IT & Systems', 'km' => 'អាយធី និងប្រព័ន្ធ']]),
            'business' => Department::updateOrCreate(['slug' => 'business'], ['name' => ['en' => 'Business & Marketing', 'km' => 'អាជីវកម្ម និងទីផ្សារ']]),
            'construction' => Department::updateOrCreate(['slug' => 'construction'], ['name' => ['en' => 'Construction', 'km' => 'ការសាងសង់']]),
            'hseq' => Department::updateOrCreate(['slug' => 'hseq'], ['name' => ['en' => 'HSEQ & Safety', 'km' => 'សុខភាព សុវត្ថិភាព និងបរិស្ថាន']]),
        ];

        // 2. Employees & OrgUnits
        $ceoEmp = Employee::updateOrCreate(
            ['name' => 'TOUCH KIM'],
            ['role' => 'CEO', 'isActive' => true, 'image' => 'employees/ceo.jpg']
        );
        $ceoUnit = OrgUnit::create([
            'employeeId' => $ceoEmp->id,
            'title' => ['en' => 'Chief Executive Officer (CEO)', 'km' => 'នាយកប្រតិបត្តិ (CEO)'],
            'type' => 'EXECUTIVE',
            'departmentId' => $depts['executive']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $dceoEmp = Employee::updateOrCreate(
            ['name' => 'RANY SOPHUAK'],
            ['role' => 'DCEO', 'isActive' => true, 'image' => 'employees/dceo.jpg']
        );
        $dceoUnit = OrgUnit::create([
            'employeeId' => $dceoEmp->id,
            'title' => ['en' => 'Deputy CEO (DCEO)', 'km' => 'អគ្គនាយករង (DCEO)'],
            'type' => 'EXECUTIVE',
            'parentId' => $ceoUnit->id,
            'departmentId' => $depts['executive']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $dgmEmp = Employee::updateOrCreate(
            ['name' => 'TOUCH RUMREAKSAY'],
            ['role' => 'DGM', 'isActive' => true, 'image' => 'employees/dgm.jpg']
        );
        $dgmUnit = OrgUnit::create([
            'employeeId' => $dgmEmp->id,
            'title' => ['en' => 'Deputy General Manager (DGM)', 'km' => 'អគ្គនាយករង (DGM)'],
            'type' => 'MANAGEMENT',
            'parentId' => $dceoUnit->id,
            'departmentId' => $depts['management']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        // Pillar 1: Technical & Projects Division
        $techDirEmp = Employee::updateOrCreate(
            ['name' => 'SENG SOPHORN'],
            ['role' => 'Technical Director', 'isActive' => true]
        );
        $techDirUnit = OrgUnit::create([
            'employeeId' => $techDirEmp->id,
            'title' => ['en' => 'Technical Director', 'km' => 'នាយកបច្ចេកទេស'],
            'type' => 'DIRECTOR',
            'parentId' => $dgmUnit->id,
            'departmentId' => $depts['technical']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $projDirEmp = Employee::updateOrCreate(
            ['name' => 'HOR SAMNANG'],
            ['role' => 'Project Director', 'isActive' => true]
        );
        $projDirUnit = OrgUnit::create([
            'employeeId' => $projDirEmp->id,
            'title' => ['en' => 'Project Director', 'km' => 'នាយកគ្រប់គ្រងគម្រោង'],
            'type' => 'DIRECTOR',
            'parentId' => $techDirUnit->id,
            'departmentId' => $depts['project-mgmt']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $mepEmp = Employee::updateOrCreate(['name' => 'KIM KEAK'], ['role' => 'MEP Operation Manager', 'isActive' => true]);
        OrgUnit::create([
            'employeeId' => $mepEmp->id,
            'title' => ['en' => 'MEP Operation Manager', 'km' => 'អ្នកគ្រប់គ្រងប្រតិបត្តិការ MEP'],
            'type' => 'MANAGER',
            'parentId' => $projDirUnit->id,
            'departmentId' => $depts['technical']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        // Pillar 2: Finance & Accounting Division
        $finDirEmp = Employee::updateOrCreate(
            ['name' => 'JENG VANNAK PH'],
            ['role' => 'Finance Director', 'isActive' => true]
        );
        $finDirUnit = OrgUnit::create([
            'employeeId' => $finDirEmp->id,
            'title' => ['en' => 'Finance Director', 'km' => 'នាយកហិរញ្ញវត្ថុ'],
            'type' => 'DIRECTOR',
            'parentId' => $dgmUnit->id,
            'departmentId' => $depts['finance']->id,
            'orderIndex' => 2,
            'isActive' => true,
        ]);

        $finMgrEmp = Employee::updateOrCreate(['name' => 'SEEY SREANA'], ['role' => 'Finance Manager', 'isActive' => true]);
        $finMgrUnit = OrgUnit::create([
            'employeeId' => $finMgrEmp->id,
            'title' => ['en' => 'Finance Manager', 'km' => 'អ្នកគ្រប់គ្រងហិរញ្ញវត្ថុ'],
            'type' => 'MANAGER',
            'parentId' => $finDirUnit->id,
            'departmentId' => $depts['finance']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $buMgrEmp = Employee::updateOrCreate(['name' => 'HUON KHOA'], ['role' => 'Business Unit Manager', 'isActive' => true]);
        OrgUnit::create([
            'employeeId' => $buMgrEmp->id,
            'title' => ['en' => 'Business Unit Manager', 'km' => 'អ្នកគ្រប់គ្រងអង្គភាពអាជីវកម្ម'],
            'type' => 'MANAGER',
            'parentId' => $finMgrUnit->id,
            'departmentId' => $depts['finance']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        // Pillar 3: Supply Chain & Procurement Division
        $scDirEmp = Employee::updateOrCreate(
            ['name' => 'CHHOURN PUTSNA'],
            ['role' => 'Supply Chain Director', 'isActive' => true]
        );
        $scDirUnit = OrgUnit::create([
            'employeeId' => $scDirEmp->id,
            'title' => ['en' => 'Supply Chain Director', 'km' => 'នាយកខ្សែសង្វាក់ផ្គត់ផ្គង់'],
            'type' => 'DIRECTOR',
            'parentId' => $dgmUnit->id,
            'departmentId' => $depts['procurement']->id,
            'orderIndex' => 3,
            'isActive' => true,
        ]);

        $procMgrEmp = Employee::updateOrCreate(['name' => 'PHO VANDY'], ['role' => 'Procurement Manager', 'isActive' => true]);
        OrgUnit::create([
            'employeeId' => $procMgrEmp->id,
            'title' => ['en' => 'Procurement Manager', 'km' => 'អ្នកគ្រប់គ្រងលទ្ធកម្ម'],
            'type' => 'MANAGER',
            'parentId' => $scDirUnit->id,
            'departmentId' => $depts['procurement']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        // Pillar 4: Corporate Administration & Legal Division
        $hrEmp = Employee::updateOrCreate(['name' => 'KEO PHALLY'], ['role' => 'HR & Liaison Manager', 'isActive' => true]);
        $hrUnit = OrgUnit::create([
            'employeeId' => $hrEmp->id,
            'title' => ['en' => 'HR & Liaison Manager', 'km' => 'អ្នកគ្រប់គ្រងធនធានមនុស្ស និងទំនាក់ទំនង'],
            'type' => 'MANAGER',
            'parentId' => $dgmUnit->id,
            'departmentId' => $depts['hr-admin']->id,
            'orderIndex' => 4,
            'isActive' => true,
        ]);

        $legalEmp = Employee::updateOrCreate(['name' => 'TY NOREN KEO'], ['role' => 'Legal Head', 'isActive' => true]);
        $legalUnit = OrgUnit::create([
            'employeeId' => $legalEmp->id,
            'title' => ['en' => 'Legal Head', 'km' => 'ប្រធានផ្នែកច្បាប់'],
            'type' => 'MANAGER',
            'parentId' => $hrUnit->id,
            'departmentId' => $depts['hr-admin']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $auditEmp = Employee::updateOrCreate(['name' => 'CHORN VUTHANAK'], ['role' => 'Internal Auditor', 'isActive' => true]);
        OrgUnit::create([
            'employeeId' => $auditEmp->id,
            'title' => ['en' => 'Internal Auditor', 'km' => 'សវនករផ្ទៃក្នុង'],
            'type' => 'MANAGER',
            'parentId' => $legalUnit->id,
            'departmentId' => $depts['management']->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        return OrgUnit::count();
    }

    /**
     * Template 2: Standard Business / SME Structure.
     */
    protected static function buildStandardCompanyTemplate(): int
    {
        $deptExec = Department::updateOrCreate(['slug' => 'executive'], ['name' => ['en' => 'Executive Office', 'km' => 'ការិយាល័យនាយកប្រតិបត្តិ']]);
        $deptOps = Department::updateOrCreate(['slug' => 'operations'], ['name' => ['en' => 'Operations', 'km' => 'ប្រតិបត្តិការ']]);
        $deptFin = Department::updateOrCreate(['slug' => 'finance'], ['name' => ['en' => 'Finance & Accounting', 'km' => 'ហិរញ្ញវត្ថុ និងគណនេយ្យ']]);
        $deptTech = Department::updateOrCreate(['slug' => 'technology'], ['name' => ['en' => 'Technology & Product', 'km' => 'បច្ចេកវិទ្យា និងផលិតផល']]);
        $deptHR = Department::updateOrCreate(['slug' => 'human-resources'], ['name' => ['en' => 'Human Resources', 'km' => 'ធនធានមនុស្ស']]);
        $deptSales = Department::updateOrCreate(['slug' => 'sales-marketing'], ['name' => ['en' => 'Sales & Marketing', 'km' => 'ផ្នែកលក់ និងទីផ្សារ']]);

        $ceo = OrgUnit::create([
            'title' => ['en' => 'Chief Executive Officer (CEO)', 'km' => 'នាយកប្រតិបត្តិ (CEO)'],
            'type' => 'EXECUTIVE',
            'departmentId' => $deptExec->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $coo = OrgUnit::create([
            'title' => ['en' => 'Chief Operating Officer (COO)', 'km' => 'ប្រធានផ្នែកប្រតិបត្តិការ (COO)'],
            'type' => 'MANAGEMENT',
            'parentId' => $ceo->id,
            'departmentId' => $deptOps->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        $cfo = OrgUnit::create([
            'title' => ['en' => 'Chief Financial Officer (CFO)', 'km' => 'ប្រធានផ្នែកហិរញ្ញវត្ថុ (CFO)'],
            'type' => 'MANAGEMENT',
            'parentId' => $ceo->id,
            'departmentId' => $deptFin->id,
            'orderIndex' => 2,
            'isActive' => true,
        ]);

        $cto = OrgUnit::create([
            'title' => ['en' => 'Chief Technology Officer (CTO)', 'km' => 'ប្រធានផ្នែកបច្ចេកវិទ្យា (CTO)'],
            'type' => 'MANAGEMENT',
            'parentId' => $ceo->id,
            'departmentId' => $deptTech->id,
            'orderIndex' => 3,
            'isActive' => true,
        ]);

        // Department Heads under Operations & Tech
        OrgUnit::create([
            'title' => ['en' => 'Operations Manager', 'km' => 'អ្នកគ្រប់គ្រងប្រតិបត្តិការ'],
            'type' => 'MANAGER',
            'parentId' => $coo->id,
            'departmentId' => $deptOps->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        OrgUnit::create([
            'title' => ['en' => 'Senior Accountant', 'km' => 'គណនេយ្យករជាន់ខ្ពស់'],
            'type' => 'MANAGER',
            'parentId' => $cfo->id,
            'departmentId' => $deptFin->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        OrgUnit::create([
            'title' => ['en' => 'Engineering Lead', 'km' => 'ប្រធានក្រុមវិស្វកម្ម'],
            'type' => 'MANAGER',
            'parentId' => $cto->id,
            'departmentId' => $deptTech->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        OrgUnit::create([
            'title' => ['en' => 'HR & Talent Manager', 'km' => 'អ្នកគ្រប់គ្រងធនធានមនុស្ស'],
            'type' => 'MANAGER',
            'parentId' => $ceo->id,
            'departmentId' => $deptHR->id,
            'orderIndex' => 4,
            'isActive' => true,
        ]);

        return OrgUnit::count();
    }

    /**
     * Template 3: Starter Root Only.
     */
    protected static function buildStarterRootTemplate(): int
    {
        $deptExec = Department::updateOrCreate(['slug' => 'executive'], ['name' => ['en' => 'Executive Office', 'km' => 'ការិយាល័យនាយកប្រតិបត្តិ']]);
        $deptOps = Department::updateOrCreate(['slug' => 'operations'], ['name' => ['en' => 'Operations', 'km' => 'ប្រតិបត្តិការ']]);
        $deptFin = Department::updateOrCreate(['slug' => 'finance'], ['name' => ['en' => 'Finance', 'km' => 'ហិរញ្ញវត្ថុ']]);
        $deptHR = Department::updateOrCreate(['slug' => 'human-resources'], ['name' => ['en' => 'Human Resources', 'km' => 'ធនធានមនុស្ស']]);

        $ceo = OrgUnit::create([
            'title' => ['en' => 'Board of Directors / CEO', 'km' => 'ក្រុមប្រឹក្សាភិបាល / នាយកប្រតិបត្តិ'],
            'type' => 'EXECUTIVE',
            'departmentId' => $deptExec->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        OrgUnit::create([
            'title' => ['en' => 'Operations Division', 'km' => 'ផ្នែកប្រតិបត្តិការ'],
            'type' => 'DIRECTOR',
            'parentId' => $ceo->id,
            'departmentId' => $deptOps->id,
            'orderIndex' => 1,
            'isActive' => true,
        ]);

        OrgUnit::create([
            'title' => ['en' => 'Finance & Administration', 'km' => 'ហិរញ្ញវត្ថុ និងរដ្ឋបាល'],
            'type' => 'DIRECTOR',
            'parentId' => $ceo->id,
            'departmentId' => $deptFin->id,
            'orderIndex' => 2,
            'isActive' => true,
        ]);

        OrgUnit::create([
            'title' => ['en' => 'Business Development', 'km' => 'អភិវឌ្ឍន៍អាជីវកម្ម'],
            'type' => 'DIRECTOR',
            'parentId' => $ceo->id,
            'departmentId' => $deptHR->id,
            'orderIndex' => 3,
            'isActive' => true,
        ]);

        return OrgUnit::count();
    }

    /**
     * Clear frontend org chart caches.
     */
    public static function clearCaches(): void
    {
        Cache::forget('about_orgchart_en');
        Cache::forget('about_orgchart_kh');
        Cache::forget('about_orgchart_km');
        Cache::forget('about_page_en');
        Cache::forget('about_page_kh');
        Cache::forget('about_page_km');
    }
}
