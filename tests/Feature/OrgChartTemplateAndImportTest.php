<?php

namespace Tests\Feature;

use App\Filament\Exports\OrgUnitExporter;
use App\Filament\Imports\OrgUnitImporter;
use App\Models\OrgUnit;
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
}
