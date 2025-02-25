<div class="overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800">
    <!-- Cabeçalho com logo e informações da empresa -->
    <div class="p-6 text-white bg-primary-600">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Decide Digital - Print</h1>
                <p class="text-sm opacity-80">Soluções em impressão digital</p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold">ORÇAMENTO</h2>
                <p class="text-lg">{{ $quote->number }}</p>
                <p class="text-sm">Data: {{ $quote->created_at->format('d/m/Y') }}</p>
                <p class="text-sm">Válido até: {{ \Carbon\Carbon::parse($quote->valid_until)->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Informações do cliente e vendedor -->
    <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2 bg-gray-50 dark:bg-gray-700">
        <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
            <h3 class="pb-2 mb-3 text-lg font-semibold border-b text-primary-600 dark:text-primary-400">Informações do Cliente</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Nome:</span> {{ $quote->client->name }}</p>
                <p><span class="font-medium">Email:</span> {{ $quote->client->email }}</p>
                @if($quote->client->phone)
                    <p><span class="font-medium">Telefone:</span> {{ $quote->client->phone }}</p>
                @endif
                @if($quote->client->address)
                    <p><span class="font-medium">Endereço:</span> {{ $quote->client->address }}</p>
                @endif
                @if($quote->client->document)
                    <p><span class="font-medium">Documento:</span> {{ $quote->client->document }}</p>
                @endif
            </div>
        </div>

        <div class="p-4 bg-white rounded-lg shadow dark:bg-gray-800">
            <h3 class="pb-2 mb-3 text-lg font-semibold border-b text-primary-600 dark:text-primary-400">Informações do Vendedor</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Nome:</span> {{ $quote->seller->name }}</p>
                <p><span class="font-medium">Email:</span> {{ $quote->seller->email }}</p>
                @if($quote->seller->phone)
                    <p><span class="font-medium">Telefone:</span> {{ $quote->seller->phone }}</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Status do orçamento -->
    <div class="p-6">
        <div class="flex items-center mb-4">
            <span class="mr-2 font-semibold">Status:</span>
            @php
                $statusColors = [
                    'draft' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    'open' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'approved' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'expired' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    'converted' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                    'canceled' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                ];
                $statusClass = $statusColors[$quote->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                {{ trans("filament-panels.resources.status.quotes.{$quote->status}") }}
            </span>
        </div>

        <!-- Itens do orçamento -->
        <h3 class="mb-3 text-lg font-semibold text-primary-600 dark:text-primary-400">Itens do Orçamentos</h3>
        <div class="overflow-x-auto" style="min-height: 70px; width: 100%;margin:10px 0;">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $item->product->name ?? 'Produto personalizado' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300">
                                {{ $item->description }}
                                
                                @if(is_array($item->customization_options))
                                    <div class="mt-2">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Opções de Personalização:</p>
                                        <ul class="mt-1 ml-2 text-xs list-disc list-inside">
                                            @foreach($item->customization_options as $option => $value)
                                                <li>{{ $option }}: {{ $value }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if(is_array($item->file_requirements))
                                    <div class="mt-2">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Requisitos de Arquivo:</p>
                                        <ul class="mt-1 ml-2 text-xs list-disc list-inside">
                                            @foreach($item->file_requirements as $req => $value)
                                                <li>{{ $req }}: {{ $value }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap dark:text-gray-300">
                                R$ {{ number_format($item->discount_amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                R$ {{ number_format(($item->quantity * $item->unit_price) - $item->discount_amount, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumo financeiro -->
    <div class="p-6 bg-gray-50 dark:bg-gray-700">
        <div class="w-full p-4 ml-auto bg-white rounded-lg shadow dark:bg-gray-800 md:w-1/3">
            <h3 class="pb-2 mb-3 text-lg font-semibold border-b text-primary-600 dark:text-primary-400">Resumo</h3>
            <div class="space-y-2">
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
    @if($quote->notes)
        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
            <h3 class="mb-3 text-lg font-semibold text-primary-600 dark:text-primary-400">Observações</h3>
            <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                {{ $quote->notes }}
            </div>
        </div>
    @endif

    <!-- Rodapé com termos e condições -->
    <div class="p-6 text-sm text-gray-600 bg-gray-100 dark:bg-gray-900 dark:text-gray-400">
        <h4 class="mb-2 font-semibold">Termos e Condições</h4>
        <ul class="space-y-1 list-disc list-inside">
            <li>Este orçamento é válido até a data especificada.</li>
            <li>Os preços podem sofrer alterações sem aviso prévio após o vencimento.</li>
            <li>O prazo de entrega será confirmado após a aprovação do orçamento.</li>
            <li>Pagamento conforme condições negociadas.</li>
        </ul>
    </div>
</div> 