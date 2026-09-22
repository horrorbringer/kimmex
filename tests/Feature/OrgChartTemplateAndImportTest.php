<?php

namespace Tests\Feature;

use App\Filament\Exports\OrgUnitExporter;
use App\Filament\Imports\OrgUnitImporter;
use App\Models\OrgUnit;
use App\Models\SystemSetting;
use App\Services\OrgStructureTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class OrgChartTemplateAndImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_template_service_creates_kimmex_corporate_hierarchy_with_relations(): void
    {
        Cache::put('about_orgchart_en', 'stale_cache');

        $count = OrgStructureTemplateService::applyTemplate('kimmex_corporate', clearExisting: true);

        $this->assertGreaterThanOrEqual(14, $count);
        $this->assertNull(Cache::get('about_orgchart_en'));

        // Check CEO exists and has no parent
        $ceo = OrgUnit::where('title->en', 'Chief Executive Officer (CEO)')->first();
        $this->assertNotNull($ceo);
        $this->assertNull($ceo->parentId);
        $this->assertSame('EXECUTIVE', $ceo->type);
        $this->assertSame('TOUCH KIM', $ceo->employee->name);

        // Check DCEO reports to CEO
        $dceo = OrgUnit::where('title->en', 'Deputy CEO (DCEO)')->first();
        $this->assertNotNull($dceo);
        $this->assertSame($ceo->id, $dceo->parentId);

        // Check DGM reports to DCEO
        $dgm = OrgUnit::where('title->en', 'Deputy General Manager (DGM)')->first();
        $this->assertNotNull($dgm);
        $this->assertSame($dceo->id, $dgm->parentId);

        // Check Finance Director reports to DGM
        $finDir = OrgUnit::where('title->en', 'Finance Director')->first();
        $this->assertNotNull($finDir);
        $this->assertSame($dgm->id, $finDir->parentId);
        $this->assertSame('DIRECTOR', $finDir->type);

        // Check Finance Manager reports to Finance Director
        $finMgr = OrgUnit::where('title->en', 'Finance Manager')->first();
        $this->assertNotNull($finMgr);
        $this->assertSame($finDir->id, $finMgr->parentId);
        $this->assertSame('MANAGER', $finMgr->type);
    }

    public function test_template_service_creates_standard_company_structure(): void
    {
        $count = OrgStructureTemplateService::applyTemplate('standard_company', clearExisting: true);

        $this->assertSame(8, $count);

        $ceo = OrgUnit::where('title->en', 'Chief Executive Officer (CEO)')->first();
        $this->assertNotNull($ceo);

        $coo = OrgUnit::where('title->en', 'Chief Operating Officer (COO)')->first();
        $this->assertNotNull($coo);
        $this->assertSame($ceo->id, $coo->parentId);

        $cfo = OrgUnit::where('title->en', 'Chief Financial Officer (CFO)')->first();
        $this->assertNotNull($cfo);
        $this->assertSame($ceo->id, $cfo->parentId);
    }

    public function test_org_unit_importer_columns_and_security_configuration(): void
    {
        $columns = OrgUnitImporter::getColumns();

        $this->assertSame([
            'title',
            'title_km',
            'type',
            'parent_title',
            'employee_name',
            'department_name',
            'order_index',
            'is_active',
        ], array_map(fn ($col): string => $col->getName(), $columns));

        $importerCode = file_get_contents(app_path('Filament/Imports/OrgUnitImporter.php'));
        $this->assertStringContainsString("where('role', 'ADMIN')->where('is_active', true)", $importerCode);
    }

    public function test_org_unit_exporter_columns_configuration(): void
    {
        $columns = OrgUnitExporter::getColumns();

        $this->assertSame([
            'title',
            'title_km',
            'type',
            'parent.title',
            'employee.name',
            'department.name',
            'orderIndex',
            'isActive',
        ], array_map(fn ($col): string => $col->getName(), $columns));
    }

    public function test_sample_csv_matches_importer_columns(): void
    {
        $example = file_get_contents(public_path('org-chart-importer-example.csv'));

        $this->assertStringStartsWith(
            'title,title_km,type,parent_title,employee_name,department_name,order_index,is_active',
            $example,
        );
        $this->assertStringContainsString('Chief Executive Officer (CEO)', $example);
        $this->assertStringContainsString('Deputy CEO (DCEO)', $example);
    }

    public function test_manage_org_chart_page_and_table_have_actions_configured(): void
    {
        $pageCode = file_get_contents(app_path('Filament/Pages/ManageOrgChart.php'));
        $tableCode = file_get_contents(app_path('Filament/Resources/OrgUnits/Tables/OrgUnitsTable.php'));

        $this->assertStringContainsString("Action::make('loadTemplate')", $pageCode);
        $this->assertStringContainsString("ImportAction::make('importOrgUnits')", $pageCode);
        $this->assertStringContainsString("ExportAction::make('exportOrgUnits')", $pageCode);
        $this->assertStringContainsString("Action::make('downloadCsvTemplate')", $pageCode);

        $this->assertStringContainsString("Action::make('loadTemplate')", $tableCode);
        $this->assertStringContainsString("ImportAction::make('importOrgUnits')", $tableCode);
        $this->assertStringContainsString("ExportAction::make('exportOrgUnits')", $tableCode);
        $this->assertStringContainsString('ExportBulkAction::make()', $tableCode);
    }

    public function test_dynamic_chart_groups_default_and_custom_configuration(): void
    {
        // 1. Defaults
        $defaults = OrgUnit::defaultChartGroups();
        $this->assertCount(3, $defaults);
        $this->assertSame('main', $defaults[0]['key']);

        $groups = OrgUnit::getChartGroups();
        $this->assertCount(3, $groups);

        $options = OrgUnit::getChartGroupOptions('en');
        $this->assertArrayHasKey('main', $options);
        $this->assertSame('Organization Structure', $options['main']);

        // 2. Custom groups saved via SystemSetting
        SystemSetting::set('org_chart_groups', [
            [
                'key' => 'main',
                'name_en' => 'Executive Board',
                'name_km' => 'គណៈកម្មាធិការប្រតិបត្តិ',
                'is_active' => true,
            ],
            [
                'key' => 'safety_team',
                'name_en' => 'Safety & QA Team',
                'name_km' => 'ក្រុមសុវត្ថិភាព',
                'is_active' => true,
            ],
            [
                'key' => 'inactive_group',
                'name_en' => 'Hidden Group',
                'name_km' => '',
                'is_active' => false,
            ],
        ]);

        $customGroups = OrgUnit::getChartGroups();
        $this->assertCount(3, $customGroups);

        $activeGroups = OrgUnit::getChartGroups(activeOnly: true);
        $this->assertCount(2, $activeGroups);

        $customOptionsEn = OrgUnit::getChartGroupOptions('en');
        $this->assertSame('Executive Board', $customOptionsEn['main']);
        $this->assertSame('Safety & QA Team', $customOptionsEn['safety_team']);

        $customOptionsKm = OrgUnit::getChartGroupOptions('km');
        $this->assertSame('គណៈកម្មាធិការប្រតិបត្តិ', $customOptionsKm['main']);
        $this->assertSame('ក្រុមសុវត្ថិភាព', $customOptionsKm['safety_team']);

        // 3. Verify ManageOrgChart page has manageGroups action
        $pageCode = file_get_contents(app_path('Filament/Pages/ManageOrgChart.php'));
        $this->assertStringContainsString("Action::make('manageGroups')", $pageCode);
    }

    public function test_card_style_templates_configuration_and_rendering(): void
    {
        $pageCode = file_get_contents(app_path('Filament/Pages/ManageOrgChart.php'));
        $this->assertStringContainsString("'org_chart_card_style'", $pageCode);
        $this->assertStringContainsString("'avatar_top'", $pageCode);
        $this->assertStringContainsString("'floating'", $pageCode);
        $this->assertStringContainsString("'badge'", $pageCode);
        $this->assertStringContainsString("'capsule'", $pageCode);
        $this->assertStringContainsString("'corporate'", $pageCode);
        $this->assertStringContainsString("'nameplate'", $pageCode);

        // Test blade component rendering with different styles
        $mockNode = [
            'name' => 'Touch Kim',
            'role' => 'Chief Executive Officer',
            'unitType' => 'EXECUTIVE',
            'image' => null,
            'children' => [],
        ];

        foreach (['avatar_top', 'floating', 'badge', 'capsule', 'corporate', 'nameplate'] as $style) {
            $html = view('components.about.tree-node', [
                'node' => $mockNode,
                'level' => 0,
                'cardStyle' => $style,
            ])->render();

            $this->assertStringContainsString('Touch Kim', $html);
            $this->assertStringContainsString('Chief Executive Officer', $html);
        }
    }

    public function test_node_independent_card_style_overrides_group_style(): void
    {
        // 1. A node with card_style 'badge' rendered inside an 'avatar_top' group
        $badgeNode = [
            'name' => 'Touch Kim',
            'role' => 'Chief Executive Officer',
            'unitType' => 'EXECUTIVE',
            'card_style' => 'badge',
            'image' => null,
            'children' => [],
        ];

        $html = view('components.about.tree-node', [
            'node' => $badgeNode,
            'level' => 0,
            'cardStyle' => 'avatar_top',
        ])->render();

        // Template 3 (badge) specific markup: h-3/4 (75% image) and h-1/4 (25% text)
        $this->assertStringContainsString('h-3/4', $html);
        $this->assertStringContainsString('h-1/4', $html);

        // 2. A node with card_style 'floating' rendered inside a 'badge' group
        $floatingNode = [
            'name' => 'Heang Meng',
            'role' => 'Deputy CEO',
            'unitType' => 'EXECUTIVE',
            'card_style' => 'floating',
            'image' => null,
            'children' => [],
        ];

        $htmlFloating = view('components.about.tree-node', [
            'node' => $floatingNode,
            'level' => 0,
            'cardStyle' => 'badge',
        ])->render();

        // Floating does not have h-3/4
        $this->assertStringNotContainsString('h-3/4', $htmlFloating);
        $this->assertStringContainsString('Heang Meng', $htmlFloating);

        // 3. OrgUnit model can persist card_style
        $unit = OrgUnit::create([
            'title' => 'Finance Director',
            'type' => 'DIRECTOR',
            'chart_group' => 'main',
            'card_style' => 'capsule',
            'isActive' => true,
            'orderIndex' => 1,
        ]);

        $this->assertSame('capsule', $unit->fresh()->card_style);
    }
}
