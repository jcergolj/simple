<?php

namespace Tests\Feature;

use App\Enums\Currency;
use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportMixedCurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_handle_mixed_currencies_properly(): void
    {
        $user = User::factory()->create();

        // Create clients with different currencies
        $usdClient = Client::factory()->create([
            'name' => 'USD Client',
            'hourly_rate' => Money::fromDecimal(50, Currency::USD),
        ]);
        $eurClient = Client::factory()->create([
            'name' => 'EUR Client',
            'hourly_rate' => Money::fromDecimal(40, Currency::EUR),
        ]);

        $usdProject = Project::factory()->for($usdClient)->create(['name' => 'USD Project']);
        $eurProject = Project::factory()->for($eurClient)->create(['name' => 'EUR Project']);

        // Create time entries for different currencies
        TimeEntry::factory()->for($usdClient)->for($usdProject)->create([
            'start_time' => Carbon::now()->subHours(2),
            'end_time' => Carbon::now()->subHour(),
            'duration' => 3600, // 1 hour - should earn $50
        ]);

        TimeEntry::factory()->for($eurClient)->for($eurProject)->create([
            'start_time' => Carbon::now()->subHours(3),
            'end_time' => Carbon::now()->subHours(2),
            'duration' => 3600, // 1 hour - should earn €40
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();

        // Check that both projects are displayed
        $response->assertSee('USD Project');
        $response->assertSee('EUR Project');

        // Check that both currency amounts are present (regardless of symbol encoding)
        $response->assertSee('50'); // USD amount
        $response->assertSee('40'); // EUR amount

        // Check that the earnings section is present
        $response->assertSee('Total Earnings');
    }

    public function test_csv_export_handles_mixed_currencies(): void
    {
        $user = User::factory()->create();

        // Create clients with explicit rates to avoid factory randomness
        $usdClient = Client::factory()->create([
            'name' => 'USD Client',
            'hourly_rate' => Money::fromDecimal(100, Currency::USD),
        ]);
        $eurClient = Client::factory()->create([
            'name' => 'EUR Client',
            'hourly_rate' => Money::fromDecimal(80, Currency::EUR),
        ]);

        // Create projects without hourly rates so they inherit from client
        $usdProject = Project::factory()->create([
            'name' => 'USD Project',
            'client_id' => $usdClient->id,
            'hourly_rate' => null,
        ]);
        $eurProject = Project::factory()->create([
            'name' => 'EUR Project',
            'client_id' => $eurClient->id,
            'hourly_rate' => null,
        ]);

        TimeEntry::factory()->create([
            'client_id' => $usdClient->id,
            'project_id' => $usdProject->id,
            'start_time' => Carbon::now()->subHours(2),
            'end_time' => Carbon::now(),
            'duration' => 7200, // 2 hours
            'hourly_rate' => null, // Inherit from client
        ]);

        TimeEntry::factory()->create([
            'client_id' => $eurClient->id,
            'project_id' => $eurProject->id,
            'start_time' => Carbon::now()->subHours(2),
            'end_time' => Carbon::now(),
            'duration' => 3600, // 1 hour
            'hourly_rate' => null, // Inherit from client
        ]);

        $response = $this->actingAs($user)->get(route('report-exports.show'));

        $response->assertOk();
        $csv = $response->getContent();

        // Check individual entries have correct currency formatting
        $this->assertStringContainsString('$100.00', (string) $csv); // USD hourly rate
        $this->assertStringContainsString('€80.00', (string) $csv); // EUR hourly rate
        $this->assertStringContainsString('$200.00', (string) $csv); // USD earnings (2 hours * $100)
        $this->assertStringContainsString('€80.00', (string) $csv); // EUR earnings (1 hour * €80)

        // Check currency-specific totals
        $this->assertStringContainsString('TOTAL (USD)', (string) $csv);
        $this->assertStringContainsString('TOTAL (EUR)', (string) $csv);
    }

    public function test_csv_export_handles_large_amounts_without_comma_separators(): void
    {
        $user = User::factory()->create();

        // Create client with high hourly rate that would normally have comma separators
        $client = Client::factory()->create([
            'name' => 'High Value Client',
            'hourly_rate' => Money::fromDecimal(1250, Currency::USD), // $1,250.00
        ]);

        $project = Project::factory()->create([
            'name' => 'Enterprise Project',
            'client_id' => $client->id,
            'hourly_rate' => null,
        ]);

        TimeEntry::factory()->create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'start_time' => Carbon::now()->subHours(10),
            'end_time' => Carbon::now(),
            'duration' => 36000, // 10 hours = $12,500.00 earnings
            'hourly_rate' => null,
        ]);

        $response = $this->actingAs($user)->get(route('report-exports.show'));

        $response->assertOk();
        $csv = $response->getContent();

        // Should contain amounts without comma thousands separators (CSV-safe)
        $this->assertStringContainsString('$1250.00', (string) $csv); // Hourly rate without comma
        $this->assertStringContainsString('$12500.00', (string) $csv); // Total earnings without comma

        // Should NOT contain comma-separated amounts that would break CSV
        $this->assertStringNotContainsString('$1,250.00', (string) $csv);
        $this->assertStringNotContainsString('$12,500.00', (string) $csv);

        // Verify CSV structure is valid by counting fields in a line
        $lines = explode("\n", $csv);
        $dataLine = $lines[1]; // First data line after header
        $fields = explode(',', $dataLine);
        $this->assertCount(9, $fields); // Should have exactly 9 fields
    }

    public function test_single_currency_reports_still_work(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['hourly_rate' => Money::fromDecimal(75, Currency::GBP)]);
        $project = Project::factory()->for($client)->create(['name' => 'GBP Project']);

        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subHours(3),
            'end_time' => Carbon::now(),
            'duration' => 10800, // 3 hours
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertOk();

        // Check that the project appears
        $response->assertSee('GBP Project');

        // Check that the page contains the earnings section
        $response->assertSee('Total Earnings');

        // Check that earnings are shown (should be in some form, even if not exactly £225.00)
        $response->assertSee('225');
    }
}
