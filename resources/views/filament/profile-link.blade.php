<x-filament::dropdown.list.item
    :color="'primary'"
    icon="heroicon-m-user"
    :href="route('filament.admin.resources.users.edit', ['record' => auth()->id()])"
    tag="a"
>
    Meu Perfil
</x-filament::dropdown.list.item> 