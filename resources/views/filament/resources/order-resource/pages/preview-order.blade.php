@php
    $statusColors = [
        'pending_payment' => 'warning',
        'processing' => 'info',
        'in_production' => 'info',
        'completed' => 'success',
        'awaiting_pickup' => 'info',
        'in_transit' => 'info',
        'delivered' => 'success',
        'returned' => 'danger',
        'finished' => 'success',
        'canceled' => 'danger',
    ];
    
    $paymentStatusColors = [
        'pending' => 'warning',
        'paid' => 'success',
        'failed' => 'danger',
        'refunded' => 'danger',
    ];
@endphp

<x-filament-panels::page>
    <div class="w-full mx-auto">
        <div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
            <!-- Cabeçalho com logo e informações da empresa -->
            <div class="p-6 text-white bg-primary-600">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Decide Digital - Print</h1>
                        <p class="text-sm opacity-80">Soluções em impressão digital</p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-xl font-bold">PEDIDO</h2>
                        <p class="text-lg">{{ $order->number }}</p>
                        <p class="text-sm">Data: {{ $order->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Informações do cliente e vendedor -->
            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2 bg-gray-50 dark:bg-gray-700">
                <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                    <h3 class="pb-2 mb-3 text-lg font-semibold border-b text-primary-600 dark:text-primary-400">Informações do Cliente</h3>
                    <div class="space-y-2" style="margin:10px 0;">
                        <p><span class="font-medium">Nome:</span> {{ $order->quote->client->name ?? 'N/A' }}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->quote->client->email ?? 'N/A' }}</p>
                        @if(isset($order->quote->client->phone) && $order->quote->client->phone)
                            <p><span class="font-medium">Telefone:</span> {{ $order->quote->client->phone }}</p>
                        @endif
                        @if(isset($order->quote->client->address) && $order->quote->client->address)
                            <p><span class="font-medium">Endereço:</span> {{ $order->quote->client->address }}</p>
                        @endif
                        @if(isset($order->quote->client->document) && $order->quote->client->document)
                            <p><span class="font-medium">Documento:</span> {{ $order->quote->client->document }}</p>
                        @endif
                    </div>
                </div>

                <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
                    <h3 class="pb-2 mb-3 text-lg font-semibold border-b text-primary-600 dark:text-primary-400">Informações do Vendedor</h3>
                    <div class="space-y-2"  style="margin:10px 0;">
                        <p><span class="font-medium">Nome:</span> {{ $order->quote->seller->name ?? 'N/A' }}</p>
                        <p><span class="font-medium">Email:</span> {{ $order->quote->seller->email ?? 'N/A' }}</p>
                        @if(isset($order->quote->seller->phone) && $order->quote->seller->phone)
                            <p><span class="font-medium">Telefone:</span> {{ $order->quote->seller->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status do pedido -->
            <div class="p-6">
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <div class="flex items-center">
                        <span class="mr-2 font-semibold">Status do Pedido:</span>
                        @php
                            $statusColors = [
                                'pending_payment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'in_production' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'awaiting_pickup' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'in_transit' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'delivered' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'returned' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                'finished' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                'canceled' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                            ];
                            $statusClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusClass }}">
                            {{ trans("filament-panels.resources.status.orders.{$order->status}") }}
                        </span>
                    </div>
                    
                    @if($order->payment_status)
                        <div class="flex items-center">
                            <span class="mr-2 font-semibold">Pagamento:</span>
                            @php
                                $paymentStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                    'failed' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                    'refunded' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                ];
                                $paymentStatusClass = $paymentStatusColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $paymentStatusClass }}">
                                {{ trans("filament-panels.resources.status.payment_status.{$order->payment_status}") }}
                            </span>
                        </div>
                    @endif
                    
                    @if($order->payment_method)
                        <div class="flex items-center">
                            <span class="mr-2 font-semibold">Método:</span>
                            <span class="px-3 py-1 text-sm font-medium text-gray-800 bg-gray-100 rounded-full dark:bg-gray-900 dark:text-gray-200">
                                {{ trans("filament-panels.resources.status.payment_method.{$order->payment_method}") }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Itens do pedido -->
                <h3 class="mb-3 text-lg font-semibold text-primary-600 dark:text-primary-400">Itens do Pedido</h3>
                <div class="overflow-x-auto" style="min-height: 70px; width: 100%;margin:10px 0;">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" style="width: 100%;">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Produto</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Descrição</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Quantidade</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Preço Unitário</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Desconto</th>
                                <th scope="col" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase dark:text-gray-300">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($items as $item)
                                @if(is_object($item) && !is_bool($item))
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $item->product->name ?? 'Produto personalizado' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                        @if(isset($item->description) && !is_bool($item->description))
                                            {{ $item->description }}
                                        @endif
                                        
                                        @php
                                            // Inicializa variáveis com valores seguros
                                            $customizationOptions = null;
                                            $fileRequirements = null;
                                            
                                            // Verifica se o item não é booleano antes de acessar propriedades
                                            if (!is_bool($item) && is_object($item)) {
                                                // Tenta decodificar se for uma string JSON
                                                if (isset($item->customization_options) && !is_bool($item->customization_options)) {
                                                    $customizationOptions = $item->customization_options;
                                                    if (is_string($customizationOptions) && !empty($customizationOptions)) {
                                                        try {
                                                            $decoded = json_decode($customizationOptions, true);
                                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                                $customizationOptions = $decoded;
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Mantém o valor original se falhar
                                                        }
                                                    }
                                                }
                                                
                                                // Tenta decodificar se for uma string JSON
                                                if (isset($item->file_requirements) && !is_bool($item->file_requirements)) {
                                                    $fileRequirements = $item->file_requirements;
                                                    if (is_string($fileRequirements) && !empty($fileRequirements)) {
                                                        try {
                                                            $decoded = json_decode($fileRequirements, true);
                                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                                $fileRequirements = $decoded;
                                                            }
                                                        } catch (\Exception $e) {
                                                            // Mantém o valor original se falhar
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @if((is_array($customizationOptions) || is_object($customizationOptions)) && !is_null($customizationOptions))
                                            <div class="mt-2">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Opções de Personalização:</p>
                                                <ul class="mt-1 ml-2 text-xs list-disc list-inside">
                                                    @foreach((array)$customizationOptions as $option => $value)
                                                        <li>
                                                            {{ $option }}:
                                                            @if(is_array($value))
                                                                <ul class="ml-2 list-disc list-inside">
                                                                    @foreach($value as $val)
                                                                        <li>{{ $val }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @elseif(is_string($customizationOptions) && !empty(trim($customizationOptions)))
                                            <div class="mt-2">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Opções de Personalização:</p>
                                                <div class="mt-1 ml-2 text-xs">
                                                    {{ $customizationOptions }}
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if((is_array($fileRequirements) || is_object($fileRequirements)) && !is_null($fileRequirements))
                                            <div class="mt-2">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Requisitos de Arquivo:</p>
                                                <ul class="mt-1 ml-2 text-xs list-disc list-inside">
                                                    @foreach((array)$fileRequirements as $req => $value)
                                                        <li>{{ $req }}: {{ $value }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @elseif(is_string($fileRequirements) && !empty(trim($fileRequirements)))
                                            <div class="mt-2">
                                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Requisitos de Arquivo:</p>
                                                <div class="mt-1 ml-2 text-xs">
                                                    {{ $fileRequirements }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                        {{ $item->quantity ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                        R$ {{ number_format($item->unit_price ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                        R$ {{ number_format($item->discount_amount ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        @php
                                            $quantity = $item->quantity ?? 0;
                                            $unitPrice = $item->unit_price ?? 0;
                                            $discountAmount = $item->discount_amount ?? 0;
                                            $itemTotal = ($quantity * $unitPrice) - $discountAmount;
                                        @endphp
                                        R$ {{ number_format($itemTotal, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Resumo financeiro -->
            <div class="p-6 bg-gray-50 dark:bg-gray-700">
                <div class="w-full p-4 ml-auto bg-white rounded-lg shadow dark:bg-gray-800 md:w-1/3">
                    <h3 class="pb-2 mb-3 text-lg font-semibold border-b text-primary-600 dark:text-primary-400">Resumo</h3>
                    <div class="space-y-2"  style="margin:10px 0">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Desconto:</span>
                            <span>R$ {{ number_format($discount, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between pt-2 text-lg font-bold border-t">
                            <span>Total:</span>
                            <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Observações -->
            @if($order->notes)
                <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="mb-3 text-lg font-semibold text-primary-600 dark:text-primary-400">Observações</h3>
                    <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                        {{ $order->notes }}
                    </div>
                </div>
            @endif

            <!-- Rodapé com termos e condições -->
            <div class="p-6 text-sm text-gray-600 bg-gray-100 dark:bg-gray-900 dark:text-gray-400">
                <h4 class="mb-2 font-semibold">Termos e Condições</h4>
                <ul class="space-y-1 list-disc list-inside">
                    <li>O prazo de entrega será confirmado após a aprovação do pagamento.</li>
                    <li>Pagamento conforme condições negociadas.</li>
                    <li>Qualquer alteração deve ser comunicada imediatamente.</li>
                    <li>Verifique todos os detalhes do pedido antes da produção.</li>
                </ul>
            </div>
        </div>
    </div>
</x-filament-panels::page> 