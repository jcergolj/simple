<turbo-frame id="project-create-form">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
        <div>
            <h2 class="text-lg sm:text-xl font-medium text-gray-900 mb-1">{{ __('Add New Project') }}</h2>
            <p class="text-gray-600 text-sm sm:text-base">{{ __('Create a new project and organize your work.') }}</p>
        </div>
        <a href="{{ route('turbo.projects.create') }}" class="bg-gray-900 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-md font-medium hover:bg-gray-800 transition-colors inline-flex items-center justify-center space-x-2 w-full sm:w-auto">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <span>{{ __('Add Project') }}</span>
        </a>
    </div>
</turbo-frame>
