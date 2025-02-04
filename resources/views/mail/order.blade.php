<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 25px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 10px 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .logo {
            margin-bottom: 10px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
        .info {
            background-color: #f3f4f6;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 5px solid #2563eb;
        }
        .products {
            margin-top: 20px;
        }
        .products h3 {
            margin-bottom: 10px;
        }
        .products table {
            width: 100%;
            border-collapse: collapse;
        }
        .products th, .products td {
            padding: 10px;
            text-align: left;
            border: 1px solid #e5e7eb;
        }
        .products th {
            background-color: #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>{{ auth()->user()->tenant->company_name ?? config('app.name') }}</h1>
        </div>
        <h2>Pedido #{{ $order->number }}</h2>
    </div>

    <div class="content">
        <p>Prezado(a) cliente,</p>
        
        <div class="info">
            <p>Seu pedido foi processado com sucesso. Em anexo você encontrará todos os detalhes do pedido.</p>
            <p><strong>Data do Pedido:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
            <p><strong>Valor Total:</strong> R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</p>
            <p><strong>Vendedor(a):</strong> {{ $order->quote->seller->name ?? 'Não especificado' }}</p>
        </div>

        <div class="products">
            <h3>Produtos do Pedido</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #e5e7eb;">Produto</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #e5e7eb;">Quantidade</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #e5e7eb;">Valor Unitário</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #e5e7eb;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->quote->items as $item)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #e5e7eb;">{{ $item->product->name }}</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #e5e7eb;">{{ $item->quantity }}</td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #e5e7eb;">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #e5e7eb;">R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p>Se você tiver alguma dúvida sobre seu pedido, não hesite em nos contatar.</p>
        
        <p>Agradecemos a preferência!</p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>
