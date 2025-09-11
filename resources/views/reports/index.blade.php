<x-layouts.app :title="__('Reports')">
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="text-center py-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Reports') }}</h1>
            <p class="text-gray-600">{{ __('View detailed time tracking reports and export data.') }}</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-xl font-medium text-gray-900 mb-4">{{ __('Report Filters') }}</h2>
            <form method="GET" action="{{ route('reports.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('From Date') }}</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('To Date') }}</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                    </div>
                    <div class="flex items-end">
                        <div class="flex gap-2 w-full">
                            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded-md font-medium hover:bg-gray-800 transition-colors flex items-center space-x-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span>{{ __('Generate') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('reports.export', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md font-medium hover:bg-green-700 transition-colors flex items-center space-x-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>{{ __('Export CSV') }}</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="text-gray-600 hover:text-gray-900 px-4 py-2 font-medium transition-colors">
                        {{ __('Clear Filters') }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <div class="text-sm text-gray-500 mb-1">{{ __('Total Hours') }}</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($totalHours, 1) }}h</div>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200">
                <div class="text-sm text-gray-500 mb-1">{{ __('Total Earnings') }}</div>
                <div class="text-2xl font-bold text-gray-900">${{ number_format($totalEarnings, 2) }}</div>
            </div>
        </div>

        <!-- Detailed Time Entries -->
        @if($timeEntries->isNotEmpty())
            <div class="bg-white rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-xl font-medium text-gray-900">{{ __('Detailed Time Entries') }} ({{ $timeEntries->count() }} {{ Str::plural('entry', $timeEntries->count()) }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-6 text-sm font-medium text-gray-900">{{ __('Date & Time') }}</th>
                                <th class="text-left py-3 px-6 text-sm font-medium text-gray-900">{{ __('Client / Project') }}</th>
                                <th class="text-left py-3 px-6 text-sm font-medium text-gray-900">{{ __('Duration') }}</th>
                                <th class="text-left py-3 px-6 text-sm font-medium text-gray-900">{{ __('Hourly Rate') }}</th>
                                <th class="text-left py-3 px-6 text-sm font-medium text-gray-900">{{ __('Earnings') }}</th>
                                <th class="text-left py-3 px-6 text-sm font-medium text-gray-900">{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timeEntries as $timeEntry)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-gray-900">{{ $timeEntry->start_time->format('M j, Y') }}</div>
                                        <div class="text-sm text-gray-500">
                                            {{ $timeEntry->start_time->format('g:i A') }} - {{ $timeEntry->end_time->format('g:i A') }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-medium text-gray-900">{{ $timeEntry->client?->name ?? 'No Client' }}</div>
                                        <div class="text-sm text-gray-500">{{ $timeEntry->project?->name ?? 'No Project' }}</div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-mono text-gray-900">{{ gmdate('H:i:s', $timeEntry->duration) }}</div>
                                    </td>
                                    <td class="py-4 px-6 text-gray-900">
                                        {{ $timeEntry->getEffectiveHourlyRate()?->formatted() ?? __('Not set') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-medium text-green-600">{{ $timeEntry->calculateEarnings()?->formatted() ?? '-' }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="max-w-xs truncate text-sm text-gray-500">
                                            {{ $timeEntry->notes ?: __('No notes') }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No completed time entries found') }}</h3>
                <p class="text-gray-600">{{ __('Try adjusting your filters or add some completed time entries to see reports.') }}</p>
            </div>
        @endif
    </div>
</x-layouts.app>
