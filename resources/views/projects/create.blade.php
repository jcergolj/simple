<x-layouts.app :title="__('Create Project')">
    <div class="space-y-8" data-controller="inline-edit">
        <!-- Page Header -->
        <div class="hero bg-gradient-to-r from-primary/5 to-secondary/5 rounded-xl">
            <div class="hero-content text-center">
                <div class="max-w-lg mx-auto px-4">
                    <h1 class="text-3xl font-bold">{{ __('Create Project') }}</h1>
                    <p class="py-2 text-base-content/70">{{ __('Add a new project to organize your work and track time.') }}</p>
                </div>
            </div>
        </div>

        <!-- Create Form -->
        <div class="card bg-base-100 shadow-xl">
            @include('turbo::projects.create', ['clients' => $clients])
        </div>
    </div>
</x-layouts.app>