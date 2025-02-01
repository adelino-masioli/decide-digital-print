<x-mail::message>
# Pedido #{{ $order->id }}

Segue em anexo o pedido solicitado.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message> 