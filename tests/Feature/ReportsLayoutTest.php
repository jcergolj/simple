<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_page_has_improved_layout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();

        // Check for essential layout elements
        $response->assertSee('Report Filters');
        $response->assertSee('Quick Select');
        $response->assertSee('Generate');
        $response->assertSee('Export CSV');
        $response->assertSee('Total Hours');
        $response->assertSee('Total Earnings');
    }

    public function test_date_range_preset_functionality(): void
    {
        $user = User::factory()->create();
        Client::factory()->create();

        // Test that presets work by checking response status
        $response = $this->actingAs($user)->get(route('reports.index', [
            'date_range' => 'this_month',
        ]));

        $response->assertOk();
        $response->assertSee('Total Hours'); // Basic functionality check
    }

    public function test_export_works_with_presets(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create();

        TimeEntry::factory()->for($client)->create([
            'start_time' => Carbon::now()->startOfWeek(),
            'end_time' => Carbon::now()->startOfWeek()->addHours(2),
            'duration' => 2 * 3600,
        ]);

        $response = $this->actingAs($user)->get(route('report-exports.show', [
            'date_range' => 'this_week',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $this->assertStringContainsString('Date,Start Time,End Time', (string) $response->getContent());
    }
}
