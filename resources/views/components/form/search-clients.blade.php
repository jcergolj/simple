@props([
    'clientId' => null,
    'clientName' => null,
    'searchId' => '',
    'fieldName' => 'client_id',
    'inputName' => 'client_name',
    'placeholder' => 'Search or create client...'
])

@php
    $uniqueId = $searchId ? $searchId : 'main';
    $clientNameId = $uniqueId . '-client-name';
    $clientIdId = $uniqueId . '-client-id';
    $resultsId = $uniqueId . '-search-client-results';
@endphp

<div data-controller="search-clients" class="relative" data-search-id="{{ $uniqueId }}">
    <input type="text"
        name="{{ $inputName }}"
        id="{{ $clientNameId }}"
        autocomplete="off"
        placeholder="{{ $placeholder }}"
        class="input input-bordered input-lg w-full text-lg"
        value="{{ $clientName }}"
        data-search-clients-target="input"
        data-action="input->search-clients#query keydown->search-clients#navigate">

    <input type="hidden" name="{{ $fieldName }}" id="{{ $clientIdId }}" value="{{ $clientId }}">
    
    <div data-search-clients-target="results" id="{{ $resultsId }}" class="absolute z-50 w-full bg-base-100 rounded-lg mt-1 shadow-lg max-h-96 overflow-y-auto"></div>
</div>
