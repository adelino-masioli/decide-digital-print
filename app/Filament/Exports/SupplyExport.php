<?php

namespace App\Filament\Exports;

use App\Models\Supply;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Enums\ExportFormat;

class SupplyExport extends Exporter
{
    protected static ?string $model = Supply::class;

    public function getFileName(Export $export): string
    {
        return "insumos-{$export->id}";
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nome'),
            ExportColumn::make('supplier.name')
                ->label('Fornecedor'),
            ExportColumn::make('unit')
                ->label('Unidade'),
            ExportColumn::make('description')
                ->label('Descrição'),
            ExportColumn::make('stock')
                ->label('Estoque'),
            ExportColumn::make('min_stock')
                ->label('Estoque Mínimo'),
            ExportColumn::make('cost_price')
                ->label('Preço de Custo')
                ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.')),
        ];
    }

    public function getQuery(): Builder
    {
        return Supply::query()->with('supplier');
    }

    public function getHeading(): string
    {
        return 'Lista de Insumos';
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
        return 'Sua exportação de suprimentos foi concluída e está pronta para download.';
    }
} 