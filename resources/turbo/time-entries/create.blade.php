<turbo-frame id="time-entry-create-form">
  <div class="card-body space-y-6">

    <!-- Header -->
    <div class="mb-4">
      <h2 class="card-title text-xl mb-1">{{ __('Create New Time Entry') }}</h2>
      <p class="text-base-content/70">{{ __('Log your time spent on projects and tasks.') }}</p>
    </div>

    <!-- Form -->
    <form action="{{ route('time-entries.store') }}" method="POST" class="space-y-6">
      @csrf

      <div class="flex-1">
        <x-form.search-clients
          searchId="create-time-entry"
          fieldName="client_id"
          inputName="client_name"
        />
      </div>

      <div class="flex-1">
        <x-form.search-projects
          searchId="create-time-entry"
          fieldName="project_id"
          inputName="project_name"
        />
      </div>

                                    <button type="submit" class="btn btn-success btn-circle btn-lg" data-keyboard-shortcuts-target="startButton" title="{{ __('Start Timer') }} (Ctrl+Shift+S)">
                                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                </form>

  </div>
</turbo-frame>
