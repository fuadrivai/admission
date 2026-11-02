<?php

namespace Tests\Feature;

use App\Google\SchoolVisitGoogleSheetService;
use App\Models\GoogleSheetMaster;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class GoogleSheetMasterFeatureTest extends TestCase
{
    private $createdMasterTable = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('google_sheet_masters')) {
            require_once database_path('migrations/2026_11_02_000002_create_google_sheet_masters_table.php');
            (new \CreateGoogleSheetMastersTable())->up();
            $this->createdMasterTable = true;
        }
    }

    protected function tearDown(): void
    {
        if ($this->createdMasterTable) {
            (new \CreateGoogleSheetMastersTable())->down();
        }

        parent::tearDown();
    }

    public function test_admin_can_view_google_sheet_master_integrations(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]));

        $this->get(route('setting.google-sheet-master.index'))
            ->assertOk()
            ->assertSee('gSheet Master')
            ->assertSee('School Visit')
            ->assertSee('Enrolment')
            ->assertSee('Uniform')
            ->assertSee('Observation');
    }

    public function test_admin_can_save_school_visit_sheet_url(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]));

        $master = GoogleSheetMaster::where('integration_key', 'school_visit')->firstOrFail();
        $url = 'https://docs.google.com/spreadsheets/d/school-visit-sheet/edit';

        $this->put(route('setting.google-sheet-master.update', $master->integration_key), [
            'google_sheet_url' => $url,
        ])->assertRedirect(route('setting.google-sheet-master.index'))
            ->assertSessionHas('success', 'School Visit Google Sheet URL saved.');

        $this->assertSame($url, $master->fresh()->google_sheet_url);
    }

    public function test_admin_can_run_school_visit_sync_from_master_page(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]));

        $url = 'https://docs.google.com/spreadsheets/d/school-visit-sheet/edit';
        $googleSheet = \Mockery::mock(SchoolVisitGoogleSheetService::class);
        $googleSheet->shouldReceive('sync')->once()->andReturn($url);
        $this->app->instance(SchoolVisitGoogleSheetService::class, $googleSheet);

        $this->post(route('setting.google-sheet-master.school-visit.sync'))
            ->assertRedirect(route('setting.google-sheet-master.index'))
            ->assertSessionHas('success', 'School Visit data synced to Google Sheets.')
            ->assertSessionHas('google_sheet_url', $url);
    }

    public function test_school_visit_list_sync_button_endpoint_returns_to_list(): void
    {
        $this->actingAs(User::create([
            'name' => 'Admin User',
            'email' => 'admin-' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin',
        ]));

        $googleSheet = \Mockery::mock(SchoolVisitGoogleSheetService::class);
        $googleSheet->shouldReceive('sync')->once()->andReturn('https://docs.google.com/spreadsheets/d/sheet-id/edit');
        $this->app->instance(SchoolVisitGoogleSheetService::class, $googleSheet);

        $this->post(route('schoolvisit.google-sheet.sync'))
            ->assertRedirect(route('schoolvisit.index'))
            ->assertSessionHas('google_sheet_success', 'School Visit data synced to Google Sheets.');
    }
}
