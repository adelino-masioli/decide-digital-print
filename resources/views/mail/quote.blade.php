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
        .validity {
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <h2>Orçamento #{{ $quote->number }}</h2>
    </div>

    <div class="content">
        <p>Prezado(a) cliente,</p>
        
        <div class="info">
            <p>Conforme solicitado, segue em anexo o orçamento detalhado dos produtos/serviços.</p>
            <p><strong>Data do Orçamento:</strong> {{ $quote->created_at->format('d/m/Y') }}</p>
            <p><strong>Valor Total:</strong> R$ {{ number_format($quote->total_amount ?? 0, 2, ',', '.') }}</p>
        </div>

        <div class="validity">
            <p><strong>Validade do Orçamento:</strong> {{ optional($quote->valid_until)->format('d/m/Y') ?? 'N/A' }}</p>
        </div>

        <p>Para aprovar este orçamento ou em caso de dúvidas, por favor entre em contato conosco.</p>
        
        <p>Agradecemos a oportunidade de apresentar nossa proposta!</p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html> 