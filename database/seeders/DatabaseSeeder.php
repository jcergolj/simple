<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Seed the application's database. */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $clients = \App\Models\Client::factory(5)->create();

        $projects = collect();
        $clients->each(function ($client) use ($projects) {
            $clientProjects = \App\Models\Project::factory(2)->create(['client_id' => $client->id]);
            $projects->push(...$clientProjects);
        });

        $projects->each(function ($project) {
            \App\Models\TimeEntry::factory(8)->create([
                'client_id' => $project->client_id,
                'project_id' => $project->id,
            ]);
        });

        \App\Models\TimeEntry::factory(1)->ongoing()->create([
            'client_id' => $clients->random()->id,
            'project_id' => $projects->random()->id,
        ]);
    }
}
