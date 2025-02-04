<!DOCTYPE html>
<html>
<head>
    <title>Orçamento #{{ $quote->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            padding: 0;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #444;
        }
        .logo {
            margin-bottom: 10px;
        }
        .details {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .notes {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-left: 5px solid #ff9800;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>{{ auth()->user()->tenant->company_name ?? config('app.name') }}</h1>
        </div>
        <h2>Orçamento #{{ $quote->number }}</h2>
        <p>Data: {{ $quote->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="details">
        <h3>Detalhes do Cliente</h3>
        <p><strong>Nome:</strong> {{ $quote->client->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $quote->client->email ?? 'N/A' }}</p>
        <p><strong>Vendedor:</strong> {{ $quote->seller->name ?? 'N/A' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
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
        Total: R$ {{ number_format($quote->total_amount, 2, ',', '.') }}
    </div>

    @if($quote->notes)
    <div class="notes">
        <h3>Observações:</h3>
        <p>{{ $quote->notes }}</p>
    </div>
    @endif
</body>
</html>
