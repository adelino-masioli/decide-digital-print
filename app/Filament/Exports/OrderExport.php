<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Enums\ExportFormat;

class OrderExport extends Exporter
{
    protected static ?string $model = Order::class;

    public function getFileName(Export $export): string
    {
        return "pedidos-{$export->id}";
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('number')
                ->label('Número'),
            ExportColumn::make('quote.client.name')
                ->label('Cliente'),
            ExportColumn::make('quote.seller.name')
                ->label('Vendedor'),
            ExportColumn::make('total_amount')
                ->label('Valor Total')
                ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.')),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => trans("filament-panels.resources.status.orders.{$state}")),
            ExportColumn::make('payment_method')
                ->label('Forma de Pagamento')
                ->formatStateUsing(fn ($state) => trans("filament-panels.resources.status.payment_method.{$state}")),
            ExportColumn::make('payment_status')
                ->label('Status do Pagamento')
                ->formatStateUsing(fn ($state) => trans("filament-panels.resources.status.payment_status.{$state}")),
            ExportColumn::make('notes')
                ->label('Observações'),
        ];
    }

    public function getQuery(): Builder
    {
        return Order::query()->with(['quote.client', 'quote.seller']);
    }

    public static function getFormSchema(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return 'Lista de Pedidos';
    }

    public function getFormats(): array
    {
        return [
            ExportFormat::Xlsx,
            ExportFormat::Csv,
        ];
    }

    public function getPageOrientation(): string
    {
        return 'landscape';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Sua exportação de pedidos foi concluída e está pronta para download.';
    }
} 