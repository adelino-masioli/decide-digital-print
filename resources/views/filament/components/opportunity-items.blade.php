<div>
    @if($this->getRecord())
        @livewire(\App\Filament\Resources\OpportunityResource\RelationManagers\ItemsRelationManager::class, [
            'ownerRecord' => $this->getRecord(),
            'pageClass' => get_class($this),
        ])
    @else
        <div class="text-sm text-gray-600">
            Salve o orçamento primeiro para adicionar produtos.
        </div>
    @endif
</div> 