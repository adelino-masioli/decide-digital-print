<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Enums\ExportFormat;

class ProductExport extends Exporter
{
    protected static ?string $model = Product::class;

    public function getFileName(Export $export): string
    {
        return "produtos-{$export->id}";
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('sku')
                ->label('SKU'),
            ExportColumn::make('name')
                ->label('Nome'),
            ExportColumn::make('category.name')
                ->label('Categoria'),
            ExportColumn::make('subcategory.name')
                ->label('Subcategoria'),
            ExportColumn::make('format')
                ->label('Formato'),
            ExportColumn::make('material')
                ->label('Material'),
            ExportColumn::make('weight')
                ->label('Gramatura'),
            ExportColumn::make('finishing')
                ->label('Acabamento'),
            ExportColumn::make('color')
                ->label('Cor'),
            ExportColumn::make('production_time')
                ->label('Prazo de Produção(em dias)'),
            ExportColumn::make('min_quantity')
                ->label('Quantidade Mínima'),
            ExportColumn::make('base_price')
                ->label('Preço Base')
                ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.')),
        ];
    }

    public function getQuery(): Builder
    {
        return Product::query()->with(['category', 'subcategory']);
    }

    public static function getFormSchema(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return 'Lista de Produtos';
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
        return 'portrait';
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        return 'Sua exportação de produtos foi concluída e está pronta para download.';
    }
} 