<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
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
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
        .info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <h2>Pedido #{{ $order->number }}</h2>
    </div>

    <div class="content">
        <p>Prezado(a) cliente,</p>
        
        <div class="info">
            <p>Seu pedido foi processado com sucesso. Em anexo você encontrará todos os detalhes do pedido.</p>
            <p><strong>Data do Pedido:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
            <p><strong>Valor Total:</strong> R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</p>
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