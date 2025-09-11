@props([
    'projectId' => null,
    'projectName' => null,
    'searchId' => '',
    'fieldName' => 'project_id',
    'inputName' => 'project_name',
    'placeholder' => 'Search or create project...'
])

@php
    $uniqueId = $searchId ? $searchId : 'main';
    $projectNameId = $uniqueId . '-project-name';
    $projectIdId = $uniqueId . '-project-id';
    $resultsId = $uniqueId . '-search-project-results';
@endphp

<div data-controller="search-projects" class="relative" data-search-id="{{ $uniqueId }}">
    <input type="text"
        name="{{ $inputName }}"
        id="{{ $projectNameId }}"
        autocomplete="off"
        placeholder="{{ $placeholder }}"
        class="input input-bordered input-lg w-full text-lg"
        value="{{ $projectName }}"
        data-search-projects-target="input"
        data-action="input->search-projects#query keydown->search-projects#navigate">

    <input type="hidden" name="{{ $fieldName }}" id="{{ $projectIdId }}" value="{{ $projectId }}">

    <div data-search-projects-target="results" id="{{ $resultsId }}" class="absolute z-50 w-full bg-base-100 rounded-lg mt-1 shadow-lg max-h-96 overflow-y-auto"></div>
</div>
