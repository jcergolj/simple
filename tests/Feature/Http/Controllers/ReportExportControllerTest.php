<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Http\Controllers\ReportExportController::class)]
class ReportExportControllerTest extends TestCase
{
    #[Test]
    public function auth_middleware_is_applied(): void
    {
        $response = $this->get(route('report-exports.show'));

        $response->assertMiddlewareIsApplied('auth');
    }

    #[Test]
    public function user_can_export_reports_as_csv(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('report-exports.show', ['format' => 'csv']));

        $response->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    #[Test]
    public function report_export_generates_csv(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Joe Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Simple']);

        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subHours(2),
            'end_time' => Carbon::now()->subHour(),
            'duration' => 3600,
        ]);

        $response = $this->actingAs($user)->get(route('report-exports.show', [
            'format' => 'csv',
            'date_from' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
        ]));

        $response->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    #[Test]
    public function report_export_includes_time_entry_data(): void
    {
        $user = User::factory()->create();
        $client = Client::factory()->create(['name' => 'Jane Doe']);
        $project = Project::factory()->for($client)->create(['name' => 'Jcergolj']);

        TimeEntry::factory()->for($client)->for($project)->create([
            'start_time' => Carbon::now()->subHours(3),
            'end_time' => Carbon::now()->subHours(2),
            'duration' => 3600,
        ]);

        $response = $this->actingAs($user)->get(route('report-exports.show', [
            'format' => 'csv',
            'date_from' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
        ]));

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('Jane Doe', (string) $content);
        $this->assertStringContainsString('Jcergolj', (string) $content);
    }
}
