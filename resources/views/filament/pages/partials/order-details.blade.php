<div class="space-y-6">
    <div class="grid grid-cols-2 gap-6">
        <!-- Informações do Pedido -->
        <div class="space-y-4">
            <div>
                <h3 class="mb-3 text-lg font-medium">Informações do Pedido</h3>
                <div class="flex justify-between w-full pt-2">
                    <!-- Número -->
                    <div class="grid items-center grid-cols-3 gap-x-6">
                        <span class="text-sm text-gray-500">Número</span>
                        <span class="col-span-2 text-sm font-medium">{{ $order->number }}</span>
                    </div>

                    <!-- Data -->
                    <div class="grid items-center grid-cols-3 gap-x-6">
                        <span class="text-sm text-gray-500">Data</span>
                        <span class="col-span-2 text-sm font-medium">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <!-- Status -->
                    <div class="grid items-center grid-cols-3 gap-x-6">
                        <span class="text-sm text-gray-500">Status</span>
                        @php
                            $statusColors = [
                                'processing' => 'text-amber-600',
                                'in_production' => 'text-blue-600',
                                'completed' => 'text-green-600',
                            ];
                            $statusLabels = [
                                'processing' => 'Em Processamento',
                                'in_production' => 'Em Produção',
                                'completed' => 'Concluído',
                            ];
                        @endphp
                        <span class="text-sm font-medium col-span-2 {{ $statusColors[$order->status] ?? 'text-gray-600' }}">
                            {{ $statusLabels[$order->status] ?? 'Desconhecido' }}
                        </span>
                    </div>

                    <!-- Pagamento -->
                    <div class="grid items-center grid-cols-3 gap-x-6">
                        <span class="text-sm text-gray-500">Pagamento</span>
                        <span class="col-span-2 text-sm font-medium">
                            @if($order->payment_status === 'paid')
                                <span class="text-green-600">Pago</span>
                            @else
                                <span class="text-yellow-600">Pendente</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informações do Cliente -->
            <div>
                <h3 class="mb-3 text-lg font-medium">Cliente</h3>
                <div>
                    <dt class="text-sm text-gray-500">Nome</dt>
                    <dd class="text-sm font-medium">{{ $order->quote->client->name }}</dd>
                </div>
                <dl class="flex w-full pt-2">
                    <div>
                        <dt class="text-sm text-gray-500">Nome</dt>
                        <dd class="text-sm font-medium">{{ $order->quote->client->name }}</dd>
                    </div>
                    @if($order->quote->client->phone)
                        <div>
                            <dt class="text-sm text-gray-500">Telefone</dt>
                            <dd class="text-sm font-medium">{{ $order->quote->client->phone }}</dd>
                        </div>
                    @endif
                    @if($order->quote->client->email)
                        <div>
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm font-medium">{{ $order->quote->client->email }}</dd>
                        </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Lista de Produtos -->
        <div>
            <h3 class="mb-3 text-lg font-medium">Produtos</h3>
            <div class="space-y-3">
                @foreach($order->quote->items as $item)
                    <div class="p-3 rounded-lg bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1">
                                <h4 class="font-medium">{{ $item->product->name }}</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $item->quantity }} × R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                </p>
                            </div>
                            <span class="text-sm font-medium">
                                R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}
                            </span>
                        </div>
                        
                        @if($item->customizations)
                            <div class="pt-2 mt-2 border-t border-gray-200">
                                <p class="mb-1 text-sm font-medium text-gray-600">Personalizações:</p>
                                <ul class="text-sm text-gray-500 list-disc list-inside">
                                    @foreach((array)$item->customizations as $customization)
                                        <li>{{ $customization }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="flex items-center justify-between pt-3 mt-4 border-t">
                <span class="font-medium">Total</span>
                <span class="text-lg font-medium">
                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>
</div> 