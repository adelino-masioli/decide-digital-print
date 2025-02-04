<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9fafb;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 25px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 25px;
            border: 1px solid #e5e7eb;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
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
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }
        .info, .validity {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .info {
            background-color: #eff6ff;
            border-left: 5px solid #2563eb;
        }
        .validity {
            background-color: #fef3c7;
            border-left: 5px solid #f59e0b;
            color: #92400e;
        }
        @media (max-width: 640px) {
            body {
                padding: 15px;
            }
            .header, .content, .footer {
                padding: 15px;
            }
            .button {
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <h1>{{ auth()->user()->tenant->company_name ?? config('app.name') }}</h1>
        </div>
        <h2>Orçamento #{{ $quote->number }}</h2>
    </div>

    <div class="content">
        <p>Prezado(a) cliente,</p>
        
        <div class="info">
            <p>Conforme solicitado, segue em anexo o orçamento detalhado dos produtos/serviços.</p>
            <p><strong>Data do Orçamento:</strong> {{ $quote->created_at->format('d/m/Y') }}</p>
            <p><strong>Valor Total:</strong> R$ {{ number_format($quote->total_amount ?? 0, 2, ',', '.') }}</p>
            <p><strong>Vendedor(a):</strong> {{ $quote->seller->name ?? 'N/A' }}</p>
        </div>

        <div class="validity">
            <p><strong>Validade do Orçamento:</strong> {{ optional($quote->valid_until)->format('d/m/Y') ?? 'N/A' }}</p>
        </div>

        <div class="products" style="margin-top: 30px;">
            <h3 style="color: #374151;">Produtos/Serviços</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f3f4f6;">
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Produto</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 1px solid #e5e7eb;">Qtd</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 1px solid #e5e7eb;">Valor Unit.</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 1px solid #e5e7eb;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote->items as $item)
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">
                            {{ $item->product->name }}
                        </td>
                        <td style="padding: 12px; text-align: center; border-bottom: 1px solid #e5e7eb;">
                            {{ $item->quantity }}
                        </td>
                        <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e5e7eb;">
                            R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                        </td>
                        <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e5e7eb;">
                            R$ {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding: 12px; text-align: right; font-weight: bold;">Total:</td>
                        <td style="padding: 12px; text-align: right; font-weight: bold;">
                            R$ {{ number_format($quote->total_amount ?? 0, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <p>Para aprovar este orçamento ou em caso de dúvidas, por favor entre em contato conosco.</p>
        
        <a href="#" class="button">Aprovar Orçamento</a>
        
        <p>Agradecemos a oportunidade de apresentar nossa proposta!</p>
    </div>

    <div class="footer">
        <p>Este é um email automático, por favor não responda.</p>
        <p>&copy; {{ date('Y') }} {{ $tenant->company_name ?? config('app.name') }}. Todos os direitos reservados.</p>
    </div>
</body>
</html>
