<div class="space-y-4">
    <div class="space-y-2">
        @foreach($order->quote->items as $item)
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-medium">{{ $item->product->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $item->product->description }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">{{ $item->quantity }} un</p>
                        <p class="text-sm text-gray-600">
                            R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
                @if($item->notes)
                    <div class="mt-2 text-sm text-gray-600">
                        <span class="font-medium">Observações:</span> {{ $item->notes }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div class="flex justify-end pt-4 border-t">
        <p class="font-medium">
            Total: R$ {{ number_format($order->total_amount, 2, ',', '.') }}
        </p>
    </div>
</div> 