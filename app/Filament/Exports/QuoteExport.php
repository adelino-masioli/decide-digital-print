<?php

namespace App\Filament\Exports;

use App\Models\Quote;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Enums\ExportFormat;

class QuoteExport extends Exporter
{
    protected static ?string $model = Quote::class;

    public function getFileName(Export $export): string
    {
        return "orcamentos-{$export->id}";
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('number')
                ->label('Número'),
            ExportColumn::make('client.name')
                ->label('Cliente'),
            ExportColumn::make('seller.name')
                ->label('Vendedor'),
            ExportColumn::make('total_amount')
                ->label('Valor Total')
                ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.')),
            ExportColumn::make('valid_until')
                ->label('Válido até')
                ->formatStateUsing(fn ($state) => date('d/m/Y', strtotime($state))),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => trans("filament-panels.resources.status.quotes.{$state}")),
            ExportColumn::make('notes')
                ->label('Observações'),
        ];
    }

    public function getQuery(): Builder
    {
        return Quote::query()->with(['client', 'seller']);
    }

    public static function getFormSchema(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return 'Lista de Orçamentos';
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
        return 'Sua exportação de orçamentos foi concluída e está pronta para download.';
    }
} 