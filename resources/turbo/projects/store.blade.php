<turbo-stream action="replace" target="project-create-form">
    <template>
        <x-project-create-button />
    </template>
</turbo-stream>

<turbo-stream action="replace" target="projects-lists">
    <template>
        <turbo-frame id="projects-lists">
            @include('turbo::projects.list', ['projects' => $projects])
        </turbo-frame>
    </template>
</turbo-stream>
