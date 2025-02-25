<div>
@php
    use App\Models\Order;
@endphp

<x-filament-panels::page>
    <div x-data="{}" class="flex flex-col space-y-8">
        <!-- Cabeçalho com estatísticas -->
        <div class="p-6 bg-white shadow-sm rounded-xl">
            <div class="flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
                <div class="flex flex-col">
                    <h2 class="text-2xl font-bold text-gray-900">Ordens de Produção</h2>
                    <div class="flex flex-wrap items-center gap-3 mt-3">
                        <span class="text-sm font-medium text-gray-500">
                            Total: {{ $columns['processing']['orders']->count() + $columns['in_production']['orders']->count() + $columns['completed']['orders']->count() }} pedidos
                        </span>
                        <span class="px-3 py-1.5 text-xs font-medium rounded-full" style="background-color: #fef3c7; color: #92400e;">
                            {{ $columns['processing']['orders']->count() }} em processamento
                        </span>
                        <span class="px-3 py-1.5 text-xs font-medium rounded-full" style="background-color: #dbeafe; color: #1e40af;">
                            {{ $columns['in_production']['orders']->count() }} em produção
                        </span>
                        <span class="px-3 py-1.5 text-xs font-medium rounded-full" style="background-color: #d1fae5; color: #065f46;">
                            {{ $columns['completed']['orders']->count() }} concluídos
                        </span>
                    </div>
                </div>
                
                <div class="w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <div class="absolute inset-y-0 flex items-center pl-3 pointer-events-none" style="left: 10px;">
                            <x-filament::icon
                                icon="heroicon-m-magnifying-glass"
                                class="w-5 h-5 text-gray-400"
                            />
                        </div>
                        
                        <x-filament::input 
                            type="search"
                            wire:model.live.debounce.500ms="search"
                            placeholder="Buscar pedidos..."
                            class="w-full h-10 pl-10"
                            style="padding-left: 35px; border:1px solid #e5e7eb; border-radius: 0.5rem;"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Colunas do Kanban -->
        <div class="grid grid-cols-1 gap-6 pt-6 md:grid-cols-3">
            @foreach ($columns as $status => $column)
                @php
                    $iconData = $this->getStatusIcon($status);
                    $headerColor = match($status) {
                        'processing' => 'style="background-color: #f59e0b; color: white;"',
                        'in_production' => 'style="background-color: #3b82f6; color: white;"',
                        'completed' => 'style="background-color: #10b981; color: white;"',
                        default => 'style="background-color: #6b7280; color: white;"'
                    };
                    
                    $iconColor = match($status) {
                        'processing' => 'style="color: #f59e0b;"',
                        'in_production' => 'style="color: #3b82f6;"',
                        'completed' => 'style="color: #10b981;"',
                        default => 'style="color: #6b7280;"'
                    };
                    
                    $borderColor = match($status) {
                        'processing' => 'style="border-color: #fef3c7;"',
                        'in_production' => 'style="border-color: #dbeafe;"',
                        'completed' => 'style="border-color: #d1fae5;"',
                        default => 'style="border-color: #f3f4f6;"'
                    };
                    
                    $bgColor = match($status) {
                        'processing' => 'style="background-color: #fffbeb;"',
                        'in_production' => 'style="background-color: #eff6ff;"',
                        'completed' => 'style="background-color: #ecfdf5;"',
                        default => 'style="background-color: #f9fafb;"'
                    };
                @endphp
                
                <div class="flex flex-col h-full border shadow-sm rounded-xl" {{ $borderColor }} {{ $bgColor }}>
                    <!-- Cabeçalho da Coluna -->
                    <div class="flex items-center justify-between p-4 rounded-t-xl" {{ $headerColor }}>
                        <div class="flex items-center gap-3">
                            <x-filament::icon
                                :icon="$iconData['icon']"
                                class="w-6 h-6"
                            />
                            <span class="text-base font-medium">{{ $column['title'] }}</span>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-medium bg-white bg-opacity-30 rounded-full">
                            {{ $column['orders']->count() }}
                        </span>
                    </div>

                    <!-- Área de Drop -->
                    <div 
                        class="flex-1 p-4 overflow-y-auto max-h-[calc(100vh-18rem)]"
                        @dragover.prevent="
                            $event.target.closest('[data-droppable]').classList.add('ring-2');
                            $event.target.closest('[data-droppable]').classList.add('scale-[1.01]');
                        "
                        @dragleave="
                            $event.target.closest('[data-droppable]').classList.remove('ring-2');
                            $event.target.closest('[data-droppable]').classList.remove('scale-[1.01]');
                        "
                        @drop.prevent="
                            $event.target.closest('[data-droppable]').classList.remove('ring-2');
                            $event.target.closest('[data-droppable]').classList.remove('scale-[1.01]');
                            const orderData = JSON.parse($event.dataTransfer.getData('text/plain'));
                            $dispatch('updateOrderStatus', {
                                orderId: orderData.id,
                                status: '{{ $status }}'
                            })
                        "
                        data-droppable
                        class="transition-all duration-300 ease-in-out rounded-b-xl"
                    >
                        <div class="space-y-4">
                            @foreach ($column['orders'] as $order)
                                <div 
                                    x-data="{
                                        showActions: false,
                                        highlight: false
                                    }"
                                    @mouseenter="showActions = true"
                                    @mouseleave="showActions = false"
                                    class="relative transition-all duration-200 bg-white border border-gray-100 rounded-lg shadow-sm cursor-move hover:shadow-md"
                                    :class="{ 'ring-2 ring-primary-500 scale-[1.02]': highlight }"
                                    draggable="true"
                                    x-init="
                                        $el.addEventListener('dragstart', (e) => {
                                            highlight = true;
                                            e.dataTransfer.setData('text/plain', JSON.stringify({
                                                id: {{ $order->id }},
                                                number: '{{ $order->number }}',
                                                client: '{{ addslashes($order->quote->client->name) }}',
                                                total: '{{ number_format($order->total_amount, 2, ',', '.') }}'
                                            }));
                                        });
                                        $el.addEventListener('dragend', (e) => {
                                            highlight = false;
                                        });
                                    "
                                >
                                    <!-- Status indicator -->
                                    <div class="absolute top-0 left-0 w-1.5 h-full rounded-l-lg" {{ $iconColor }}></div>
                                    
                                    <div class="p-4 pl-5">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-medium" {{ $iconColor }}>{{ $order->number }}</span>
                                                @if($order->payment_status === 'paid')
                                                    <span class="px-2 py-0.5 text-xs font-medium text-white rounded-full" style="background-color: #10b981;">
                                                        Pago
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 text-xs font-medium text-white rounded-full" style="background-color: #6b7280;">
                                                        Pendente
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <button
                                                type="button"
                                                x-on:click="$wire.openOrderDetails({{ $order->id }})"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium transition-colors rounded-md text-primary-600 hover:text-primary-500 hover:bg-primary-50"
                                                style="background-color: white; box-shadow: 0 1px 2px rgba(0,0,0,0.05);"
                                            >
                                                <x-filament::icon
                                                    icon="heroicon-m-eye"
                                                    class="w-3.5 h-3.5"
                                                />
                                                Detalhes
                                            </button>
                                        </div>

                                        <div class="text-sm font-medium text-gray-800 truncate">{{ $order->quote->client->name }}</div>
                                        
                                        <div class="flex flex-wrap gap-2 mt-3">
                                            @foreach($order->quote->items->take(2) as $item)
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">
                                                    {{ $item->product->name }}
                                                </span>
                                            @endforeach
                                            
                                            @if($order->quote->items->count() > 2)
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-gray-800 bg-gray-100 rounded-full">
                                                    +{{ $order->quote->items->count() - 2 }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex items-center justify-between pt-3 mt-4 text-xs border-t border-gray-100">
                                            <span class="text-gray-500">
                                                {{ $order->updated_at->format('d/m H:i') }}
                                            </span>
                                            <span class="font-medium text-gray-900">
                                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if($column['orders']->isEmpty())
                                <div class="flex flex-col items-center justify-center h-40 text-sm opacity-70" {{ $iconColor }}>
                                    <x-filament::icon
                                        :icon="$iconData['icon']"
                                        class="w-8 h-8 mb-3 animate-bounce"
                                    />
                                    <span class="font-medium">{{ $this->getDropZoneText($status) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal de Detalhes -->
    <x-filament::modal
        id="order-details"
        :slide-over="true"
        width="3xl"
        class="!gap-y-0"
    >
        <x-slot name="header">
            @if($selectedOrder)
                <div class="flex items-center gap-3">
                    <x-filament::icon
                        icon="heroicon-o-document-text"
                        class="w-6 h-6 text-primary-500"
                    />
                    <h2 class="text-lg font-medium">
                        Pedido {{ $selectedOrder->number }}
                    </h2>
                </div>
            @endif
        </x-slot>

        @if($selectedOrder)
            @include('filament.pages.partials.order-details', ['order' => $selectedOrder])
        @endif

        <x-slot name="footer">
            <div class="flex justify-between w-full">
                <x-filament::button
                    x-on:click="$dispatch('close-modal', { id: 'order-details' })"
                    color="gray"
                >
                    Fechar
                </x-filament::button>
                
                @if($selectedOrder)
                    <div class="flex gap-3">
                        <x-filament::button
                            tag="a"
                            :href="route('filament.admin.resources.orders.edit', $selectedOrder)"
                            color="primary"
                        >
                            Editar Pedido
                        </x-filament::button>
                    </div>
                @endif
            </div>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>
</div> 