<x-layouts.app :title="__('Time Entries')">
    <div class="space-y-8" data-controller="inline-edit">
        <!-- Page Header -->
        <div class="text-center py-2 sm:py-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">{{ __('Time Entries') }}</h1>
            <p class="text-gray-600 text-sm sm:text-base">{{ __('Track and manage your time entries efficiently.') }}</p>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-4">{{ __('Filters') }}</h3>
            <form method="GET" action="{{ route('time-entries.index') }}">
                <!-- Mobile/Tablet: Stacked layout -->
                <div class="block xl:hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Client') }}</label>
                            <select name="client_id" class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
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
                            <turbo-frame id="project-filter-mobile" src="{{ route('project-filter', ['client_id' => request('client_id'), 'selected_project_id' => request('project_id')]) }}" loading="lazy">
                                <select name="project_id" class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-gray-50" disabled>
                                    <option value="">{{ request('client_id') ? __('Loading projects...') : __('Select a client first') }}</option>
                                </select>
                            </turbo-frame>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('From Date') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                   class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-base">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('To Date') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                   class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-base">
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="submit" class="h-10 bg-gray-900 text-white px-4 py-2 rounded-md font-medium hover:bg-gray-800 transition-colors flex items-center justify-center space-x-2 w-full sm:w-auto">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>{{ __('Filter') }}</span>
                        </button>
                        <a href="{{ route('time-entries.index') }}" class="h-10 text-gray-600 hover:text-gray-900 px-4 py-2 font-medium transition-colors text-center flex items-center justify-center">
                            {{ __('Clear Filters') }}
                        </a>
                    </div>
                </div>

                <!-- Desktop XL: Single line layout -->
                <div class="hidden xl:block">
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Client') }}</label>
                            <select name="client_id" class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                                <option value="">{{ __('All Clients') }}</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Project') }}</label>
                            <turbo-frame id="project-filter-desktop" src="{{ route('project-filter', ['client_id' => request('client_id'), 'selected_project_id' => request('project_id')]) }}" loading="lazy">
                                <select name="project_id" class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent bg-gray-50" disabled>
                                    <option value="">{{ request('client_id') ? __('Loading projects...') : __('Select a client first') }}</option>
                                </select>
                            </turbo-frame>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('From Date') }}</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                   class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-base">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('To Date') }}</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}"
                                   class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent text-base">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="h-10 bg-gray-900 text-white px-4 py-2 rounded-md font-medium hover:bg-gray-800 transition-colors flex items-center space-x-2 whitespace-nowrap">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <span>{{ __('Filter') }}</span>
                            </button>
                            <a href="{{ route('time-entries.index') }}" class="h-10 text-gray-600 hover:text-gray-900 px-4 py-2 font-medium transition-colors flex items-center whitespace-nowrap">
                                {{ __('Clear Filters') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Add Time Entry Section -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 sm:p-6">
            <x-time-entry-create-button />
        </div>

        <!-- Time Entries List -->
        <turbo-frame id="time-entries-lists">
            @include('turbo::time-entries.list', ['timeEntries' => $timeEntries])
        </turbo-frame>
    </div>

    <script>
        // Handle client selection change - reload project filter turbo frames
        document.querySelectorAll('select[name="client_id"]').forEach(function(clientSelect) {
            clientSelect.addEventListener('change', function() {
                const selectedClientId = this.value;
                const currentProjectId = document.querySelector('select[name="project_id"]')?.value || '';
                const filterUrl = '{{ route('project-filter') }}?client_id=' + selectedClientId + '&selected_project_id=' + currentProjectId;

                // Update both mobile and desktop turbo frames
                document.getElementById('project-filter-mobile')?.setAttribute('src', filterUrl);
                document.getElementById('project-filter-desktop')?.setAttribute('src', filterUrl);
                document.getElementById('project-filter-mobile')?.reload();
                document.getElementById('project-filter-desktop')?.reload();
            });
        });
    </script>
</x-layouts.app>
