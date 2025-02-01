<!DOCTYPE html>
<html>
<head>
    <title>Orçamento #{{ $quote->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { margin-top: 20px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orçamento #{{ $quote->number }}</h1>
        <p>Data: {{ $quote->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="details">
        <h3>Detalhes do Cliente</h3>
        <p>Cliente: {{ $quote->client->name ?? 'N/A' }}</p>
        <p>Email: {{ $quote->client->email ?? 'N/A' }}</p>
        <p>Vendedor: {{ $quote->seller->name ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unit.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        <h3>Total: R$ {{ number_format($quote->total_amount, 2, ',', '.') }}</h3>
    </div>

    @if($quote->notes)
    <div class="notes" style="margin-top: 20px;">
        <h3>Observações:</h3>
        <p>{{ $quote->notes }}</p>
    </div>
    @endif
</body>
</html> 