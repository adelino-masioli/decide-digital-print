<div>
@php
    use App\Models\Order;
@endphp

<x-filament-panels::page>
    <div class="flex flex-col">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center px-2 mb-4">
            <div class="flex gap-2 items-center">
                <h2 class="text-lg font-bold">Pedidos na fila:</h2>
                <span class="text-sm text-gray-500">
                    {{ $columns['processing']['orders']->count() + $columns['in_production']['orders']->count() + $columns['completed']['orders']->count() }} pedidos
                </span>
            </div>
            
            <div class="flex gap-2 items-center">
                <x-filament::input.wrapper>
                    <x-filament::input 
                        type="search"
                        wire:model.live.debounce.500ms="search"
                        placeholder="Buscar pedidos..."
                    />
                </x-filament::input.wrapper>
            </div>
        </div>

        <!-- Colunas do Kanban -->
        <div class="flex overflow-x-auto gap-4 pb-4">
            @foreach ($columns as $status => $column)
                <div class="flex-1 min-w-[320px] bg-gray-50/80 rounded-lg">
                    <!-- Cabeçalho da Coluna -->
                    <div class="flex justify-between items-center p-3">
                        <div class="flex gap-2 items-center">
                            @php
                                $iconData = $this->getStatusIcon($status);
                                $iconColor = match($status) {
                                    'processing' => 'rgb(251 146 60)',  // orange-400
                                    'in_production' => 'rgb(96 165 250)',  // blue-400
                                    'completed' => 'rgb(74 222 128)',  // green-400
                                    default => 'rgb(156 163 175)'  // gray-400
                                };
                            @endphp
                            <x-filament::icon
                                :icon="$iconData['icon']"
                                class="w-6 h-6"
                                :style="'color: ' . $iconColor"
                            />
                            <span class="text-sm font-medium">{{ $column['title'] }}</span>
                        </div>
                        <span class="px-2 py-0.5 text-xs bg-gray-200 rounded-full dark:bg-gray-800">
                            {{ $column['orders']->count() }}
                        </span>
                    </div>

                    <!-- Área de Drop -->
                    <div class="p-2">
                        @php
                            $dropZoneColor = match($status) {
                                'processing' => 'rgb(254 218 189)',  // orange-400
                                'in_production' => 'rgb(179 214 255)',  // blue-400
                                'completed' => 'rgb(171 255 202)',  // green-400
                                default => 'rgb(156 163 175)'  // gray-400
                            };
                            
                            $dropZoneBg = match($status) {
                                'processing' => 'rgb(255 247 237)',
                                'in_production' => 'rgb(247 250 255)',
                                'completed' => 'rgb(247 254 249)',
                                default => 'rgb(250 251 252)' 
                            };
                        @endphp
                        <div 
                            class="min-h-screen rounded border border-dashed transition-all duration-200"
                            style="border-color: {{ $dropZoneColor }}; background-color: {{ $dropZoneBg }};"
                            @dragover.prevent="
                                $event.target.classList.add('scale-[1.02]');
                                $event.target.style.borderColor = '{{ $dropZoneColor }}';
                                $event.target.style.backgroundColor = '{{ $dropZoneBg }}';
                            "
                            @dragleave="
                                $event.target.classList.remove('scale-[1.02]');
                            "
                            @drop.prevent="
                                $event.target.classList.remove('scale-[1.02]');
                                const orderData = JSON.parse($event.dataTransfer.getData('text/plain'));
                                $dispatch('updateOrderStatus', {
                                    orderId: orderData.id,
                                    status: '{{ $status }}'
                                })
                            "
                        >
                            <!-- Cards -->
                            <div class="p-2 space-y-2">
                                @foreach ($column['orders'] as $order)
                                    <div 
                                        x-data
                                        class="relative p-3 bg-white rounded shadow-sm transition-all duration-200 cursor-move hover:shadow-md group"
                                        draggable="true"
                                        x-init="
                                            $el.addEventListener('dragstart', (e) => {
                                                e.target.classList.add('opacity-50');
                                                e.dataTransfer.setData('text/plain', JSON.stringify({
                                                    id: {{ $order->id }},
                                                    number: '{{ $order->number }}',
                                                    client: '{{ addslashes($order->quote->client->name) }}',
                                                    total: '{{ number_format($order->total_amount, 2, ',', '.') }}'
                                                }));
                                            });
                                            $el.addEventListener('dragend', (e) => {
                                                e.target.classList.remove('opacity-50');
                                            });
                                        "
                                    >
                                        <div class="flex justify-between items-center mb-2">
                                            <div>
                                                <span class="text-sm font-medium text-primary-600">{{ $order->number }}</span>
                                                @if($order->payment_status === 'paid')
                                                    <span class="px-1.5 py-0.5 text-xs text-white bg-gray-400 rounded-full dark:bg-gray-600">
                                                        Pago
                                                    </span>
                                                @endif
                                            </div>
                                            <button
                                                type="button"
                                                x-on:click="$wire.openOrderDetails({{ $order->id }})"
                                                class="inline-flex gap-1 items-center text-sm text-primary-600 hover:text-primary-500"
                                            >
                                                <x-filament::icon
                                                    icon="heroicon-m-eye"
                                                    class="w-4 h-4"
                                                />
                                                Ver detalhes
                                            </button>
                                        </div>

                                        <div class="text-sm text-gray-600 truncate">{{ $order->quote->client->name }}</div>

                                        <div class="flex justify-between items-center mt-2 text-xs">
                                            <span class="text-gray-500">
                                                {{ $order->updated_at->format('d/m H:i') }}
                                            </span>
                                            <span class="font-medium">
                                                R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach

                                @if($column['orders']->isEmpty())
                                    <div class="flex flex-col justify-center items-center h-32 text-sm text-gray-500">
                                        <x-filament::icon
                                            icon="heroicon-o-arrow-down"
                                            class="mb-2 w-5 h-5 animate-bounce"
                                        />
                                        {{ $this->getDropZoneText($status) }}
                                    </div>
                                @endif
                            </div>
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
            <h2 class="text-lg font-medium">
                Pedido {{ $selectedOrder->number }}
            </h2>
        @endif
    </x-slot>

    @if($selectedOrder)
        @include('filament.pages.partials.order-details', ['order' => $selectedOrder])
    @endif

    <x-slot name="footer">
        <x-filament::button
            x-on:click="$dispatch('close-modal', { id: 'order-details' })"
            color="gray"
        >
            Fechar
        </x-filament::button>
    </x-slot>
</x-filament::modal>
</x-filament-panels::page> 
</div> 