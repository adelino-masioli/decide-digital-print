<?php

namespace App\Filament\Exports;

use App\Models\Category;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Enums\ExportFormat;

class CategoryExport extends Exporter
{
    protected static ?string $model = Category::class;

    public function getFileName(Export $export): string
    {
        return "categorias-{$export->id}";
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nome'),
            ExportColumn::make('full_path')
                ->label('Caminho Completo'),
            ExportColumn::make('slug')
                ->label('Slug'),
            ExportColumn::make('description')
                ->label('Descrição'),
            ExportColumn::make('is_active')
                ->label('Status')
                ->formatStateUsing(fn(bool $state): string => $state ? 'Ativo' : 'Inativo'),
        ];
    }

    public function getQuery(): Builder
    {
        return Category::query();
    }

    public static function getFormSchema(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return 'Lista de Categorias';
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
        return 'Sua exportação de categorias foi concluída e está pronta para download.';
    }
} 