@php
    use App\Models\Order;
@endphp

<x-filament-panels::page>
    <div class="flex flex-col h-[calc(100vh-8rem)]">
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between px-2 mb-4">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-bold">Quadro de Produção</h2>
                <span class="text-sm text-gray-500">
                    {{ $columns['processing']['orders']->count() + $columns['in_production']['orders']->count() + $columns['completed']['orders']->count() }} pedidos
                </span>
            </div>
            
            <div class="flex items-center gap-2">
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
        <div class="flex gap-4 pb-4 overflow-x-auto">
            @foreach ($columns as $status => $column)
                <div class="flex-1 min-w-[320px] bg-gray-50/80 rounded-lg">
                    <!-- Cabeçalho da Coluna -->
                    <div class="flex items-center justify-between p-3">
                        <div class="flex items-center gap-2">
                            @php
                                $iconData = $this->getStatusIcon($status);
                            @endphp
                            <x-filament::icon
                                :icon="$iconData['icon']"
                                @class(["w-4 h-4", $iconData['color']])
                            />
                            <span class="text-sm font-medium">{{ $column['title'] }}</span>
                        </div>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100">
                            {{ $column['orders']->count() }}
                        </span>
                    </div>

                    <!-- Área de Drop -->
                    <div class="p-2">
                        <div 
                            class="min-h-screen rounded border border-dashed transition-all duration-200 {{ $this->getDropZoneClass($status) }}"
                            @dragover.prevent="$event.target.classList.add('scale-[1.02]')"
                            @dragleave="$event.target.classList.remove('scale-[1.02]')"
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
                                        x-data="{ expanded: false }"
                                        class="relative p-3 transition-all duration-200 bg-white rounded shadow-sm cursor-move hover:shadow-md group"
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
                                        <!-- Cabeçalho do Card -->
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <span class="text-sm font-medium text-primary-600">{{ $order->number }}</span>
                                                @if($order->payment_status === 'paid')
                                                    <span class="px-1.5 py-0.5 text-xs rounded-full bg-green-100 text-green-700">
                                                        Pago
                                                    </span>
                                                @endif
                                            </div>
                                            <button
                                                type="button"
                                                @click.prevent="expanded = !expanded"
                                                class="text-gray-400 hover:text-primary-500"
                                            >
                                                <x-filament::icon
                                                    x-bind:icon="expanded ? 'heroicon-m-chevron-up' : 'heroicon-m-chevron-down'"
                                                    class="w-5 h-5"
                                                />
                                            </button>
                                        </div>

                                        <div class="text-sm text-gray-600 truncate">{{ $order->quote->client->name }}</div>

                                        <!-- Conteúdo Expandível -->
                                        <div
                                            x-show="expanded"
                                            x-collapse
                                            @click.stop
                                            class="pt-3 mt-3 space-y-3 border-t"
                                        >
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr>
                                                        <th class="text-left text-gray-600">Produto</th>
                                                        <th class="text-right text-gray-600">Qtd</th>
                                                        <th class="text-right text-gray-600">Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->quote->items as $item)
                                                        <tr>
                                                            <td class="py-1">
                                                                <div class="font-medium">{{ $item->product->name }}</div>
                                                                @if($item->customizations)
                                                                    <div class="mt-1 text-xs text-gray-500">
                                                                        @foreach((array)$item->customizations as $customization)
                                                                            <div>• {{ $customization }}</div>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="text-right">{{ $item->quantity }}</td>
                                                            <td class="text-right">R$ {{ number_format($item->unit_price * $item->quantity, 2, ',', '.') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Rodapé do Card -->
                                        <div class="flex items-center justify-between mt-2 text-xs">
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
                                    <div class="flex flex-col items-center justify-center h-32 text-sm text-gray-500">
                                        <x-filament::icon
                                            icon="heroicon-o-arrow-down"
                                            class="w-5 h-5 mb-2 animate-bounce"
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
</x-filament-panels::page> 