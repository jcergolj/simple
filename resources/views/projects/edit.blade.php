<x-layouts.app :title="__('Edit Project')">
    <div class="space-y-8" data-controller="inline-edit">
        <!-- Page Header -->
        <div class="hero bg-gradient-to-r from-primary/5 to-secondary/5 rounded-xl">
            <div class="hero-content text-center">
                <div class="max-w-md">
                    <h1 class="text-3xl font-bold">{{ __('Edit Project') }}</h1>
                    <p class="py-2 text-base-content/70">{{ __('Update project information and settings.') }}</p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="card bg-base-100 shadow-xl">
            @include('turbo::projects.edit', ['project' => $project, 'clients' => $clients])
        </div>
    </div>
</x-layouts.app>