<turbo-frame id="{{ $frameId }}">
<select name="project_id" class="w-full h-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent" {{ !$clientId ? 'disabled' : '' }}>
    <option value="">{{ $clientId ? __('All Projects') : __('Select a client first') }}</option>
    @foreach($projects as $project)
        <option value="{{ $project->id }}" {{ $selectedProjectId == $project->id ? 'selected' : '' }}>
            {{ $project->name }}
        </option>
    @endforeach
</select>
</turbo-frame>
