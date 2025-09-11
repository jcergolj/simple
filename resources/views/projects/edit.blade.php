<x-layouts.app :title="__('Edit Project')">
    <div class="space-y-8" data-controller="inline-edit">
        <!-- Page Header -->
        <div class="hero bg-gradient-to-r from-primary/5 to-secondary/5 rounded-xl">
            <div class="hero-content text-center">
                <div class="max-w-lg mx-auto px-4">
                    <h1 class="text-5xl font-bold text-gray-900">{{ __('Edit Project') }}</h1>
                    <p class="mt-4 text-xl text-gray-600">{{ __('Update project information and settings.') }}</p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="card bg-base-100 shadow-xl">
            @include('turbo::projects.edit', ['project' => $project, 'clients' => $clients])
        </div>
    </div>
</x-layouts.app>