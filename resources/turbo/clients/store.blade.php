<turbo-stream action="replace" target="client-create-form">
    <template>
        <x-client-create-button />
    </template>
</turbo-stream>

<turbo-stream action="replace" target="clients-lists">
    <template>
        <turbo-frame id="clients-lists">
            @include('turbo::clients.list', ['clients' => $clients])
        </turbo-frame>
    </template>
</turbo-stream>
