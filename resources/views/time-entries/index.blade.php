<x-layouts.app :title="__('Time Entries')">
    <div class="space-y-8" data-controller="inline-edit">
        <!-- Page Header -->
        <div class="text-center py-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Time Entries') }}</h1>
            <p class="text-gray-600">{{ __('Track and manage your time entries efficiently.') }}</p>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-xl font-medium text-gray-900 mb-4">{{ __('Filters') }}</h3>
            <form method="GET" action="{{ route('time-entries.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Client') }}</label>
                        <select name="client_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <option value="">{{ __('All Clients') }}</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Project') }}</label>
                        <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                            <option value="">{{ __('All Projects') }}</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->client->name }} - {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('From Date') }}</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('To Date') }}</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md font-medium hover:bg-gray-800 transition-colors flex items-center space-x-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>{{ __('Filter') }}</span>
                    </button>
                    <a href="{{ route('time-entries.index') }}" class="text-gray-600 hover:text-gray-900 px-4 py-2 font-medium transition-colors">
                        {{ __('Clear Filters') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Add Time Entry Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <turbo-frame id="time-entry-create-form">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-medium text-gray-900 mb-1">{{ __('Track Time') }}</h2>
                        <p class="text-gray-600">{{ __('Log your time spent on projects and tasks.') }}</p>
                    </div>
                    <a href="{{ route('turbo.time-entries.create') }}" class="bg-gray-900 text-white px-6 py-3 rounded-md font-medium hover:bg-gray-800 transition-colors inline-flex items-center space-x-2" data-turbo-frame="time-entry-create-form">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>{{ __('Add New Entry') }}</span>
                    </a>
                </div>
            </turbo-frame>
        </div>

        <!-- Time Entries List -->
        <turbo-frame id="time-entries-lists">
            @include('turbo::time-entries.list', ['timeEntries' => $timeEntries])
        </turbo-frame>
    </div>
</x-layouts.app>
