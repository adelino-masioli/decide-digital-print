<?php

namespace App\Filament\Exports;

use App\Models\Supplier;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Enums\ExportFormat;

class SupplierExport extends Exporter
{
    protected static ?string $model = Supplier::class;

    public function getFileName(Export $export): string
    {
        return "fornecedores-{$export->id}";
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nome'),
            ExportColumn::make('email')
                ->label('E-mail'),
            ExportColumn::make('phone')
                ->label('Telefone'),
            ExportColumn::make('contact_info')
                ->label('Contato'),
            ExportColumn::make('address')
                ->label('Endereço'),
            ExportColumn::make('neighborhood')
                ->label('Bairro'),
            ExportColumn::make('postal_code')
                ->label('CEP'),
            ExportColumn::make('city.name')
                ->label('Cidade'),
            ExportColumn::make('state.name')
                ->label('Estado'),
        ];
    }

    public function getQuery(): Builder
    {
        return Supplier::query()->with(['city', 'state']);
    }

    public function getHeading(): string
    {
        return 'Lista de Fornecedores';
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
        return 'Sua exportação de fornecedores foi concluída e está pronta para download.';
    }
} 