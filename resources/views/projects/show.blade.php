<x-layouts.app :title="__('Project Details')">
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="hero bg-gradient-to-r from-primary/5 to-secondary/5 rounded-xl">
            <div class="hero-content text-center">
                <div class="max-w-lg mx-auto px-4">
                    <h1 class="text-5xl font-bold text-gray-900">{{ __('Project Details') }}</h1>
                    <p class="mt-4 text-xl text-gray-600">{{ __('View detailed information about this project.') }}</p>
                </div>
            </div>
        </div>

        <!-- Project Details -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body space-y-6">

                <!-- Header with Actions -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-4">
                        <div class="avatar placeholder">
                            <div class="bg-secondary text-secondary-content rounded-full w-16 h-16">
                                <span class="text-xl font-bold">{{ strtoupper(substr($project->name, 0, 2)) }}</span>
                            </div>
                        </div>
                        <div>
                            <h2 class="card-title text-2xl">{{ $project->name }}</h2>
                            <p class="text-base-content/70">Client: {{ $project->client->name }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 sm:mt-0">
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ route('projects.index') }}" class="btn btn-ghost">Back to List</a>
                    </div>
                </div>

                <!-- Project Stats -->
                @if($project->timeEntries->isNotEmpty())
                    <div class="stats stats-vertical sm:stats-horizontal shadow bg-base-200">
                        <div class="stat">
                            <div class="stat-figure text-primary">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="stat-title">Time Entries</div>
                            <div class="stat-value text-primary">{{ $project->timeEntries->count() }}</div>
                            <div class="stat-desc">Total entries logged</div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">Total Duration</div>
                            @php
                                $totalSeconds = $project->timeEntries->sum('duration');
                                $totalHours = floor($totalSeconds / 3600);
                                $totalMinutes = floor(($totalSeconds % 3600) / 60);
                            @endphp
                            <div class="stat-value text-success">{{ $totalHours }}h {{ $totalMinutes }}m</div>
                            <div class="stat-desc">Time tracked</div>
                        </div>
                    </div>
                @endif

                <!-- Details Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Project Information -->
                        <div class="card bg-base-200 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Project Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-sm text-base-content/70">Project Name</div>
                                        <div class="font-medium">{{ $project->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-base-content/70">Client</div>
                                        <div class="font-medium">{{ $project->client->name }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-base-content/70">Description</div>
                                        <div class="font-medium">{{ $project->description ?: 'No description provided' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Billing Information -->
                        <div class="card bg-base-200 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-lg">Billing Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <div class="text-sm text-base-content/70">Project Hourly Rate</div>
                                        <div class="font-medium">
                                            @if($project->hourly_rate)
                                                {{ $project->hourly_rate->formatted() }}/hr
                                            @else
                                                <span class="text-base-content/50">Uses client rate</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-base-content/70">Client Default Rate</div>
                                        <div class="font-medium">
                                            @if($project->client->hourly_rate)
                                                {{ $project->client->hourly_rate->formatted() }}/hr
                                            @else
                                                <span class="text-base-content/50">Not set</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Time Entries -->
                @if($project->timeEntries->isNotEmpty())
                    <div class="card bg-base-200 shadow-sm">
                        <div class="card-body">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="card-title text-lg">Recent Time Entries</h3>
                                <a href="{{ route('time-entries.index', ['project_id' => $project->id]) }}" class="btn btn-2xl btn-primary">View All</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="table table-zebra">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Duration</th>
                                            <th>Notes</th>
                                            <th>Earnings</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->timeEntries->take(5) as $timeEntry)
                                            <tr>
                                                <td>
                                                    <div class="font-medium"><x-user-date :date="$timeEntry->start_time" /></div>
                                                    <div class="text-sm text-base-content/70"><x-user-time :time="$timeEntry->start_time" /></div>
                                                </td>
                                                <td>
                                                    <div class="badge badge-primary">{{ $timeEntry->getFormattedDuration() }}</div>
                                                </td>
                                                <td>
                                                    <div class="max-w-xs truncate">{{ $timeEntry->notes ?: 'No notes' }}</div>
                                                </td>
                                                <td>
                                                    @if($timeEntry->calculateEarnings())
                                                        @php
                                                            $earnings = $timeEntry->calculateEarnings();
                                                            $currency = App\Enums\Currency::from($earnings->currency);
                                                        @endphp
                                                        <div class="font-medium text-success">{{ $currency->symbol() }}{{ number_format($earnings->amount, 2) }}</div>
                                                    @else
                                                        <span class="text-base-content/50">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Delete Action -->
                <div class="card bg-error/5 border border-error/20 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-error">Danger Zone</h3>
                        <p class="text-base-content/70 mb-4">Delete this project and all associated time entries permanently. This action cannot be undone.</p>
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this project? This will also delete all associated time entries.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Project
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
