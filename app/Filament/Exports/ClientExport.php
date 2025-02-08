<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Enums\ExportFormat;

class ClientExport extends Exporter
{
    protected static ?string $model = User::class;

    public function getFileName(Export $export): string
    {
        return "clientes-{$export->id}";
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Nome'),
            ExportColumn::make('last_name')
                ->label('Sobrenome'),
            ExportColumn::make('email')
                ->label('E-mail'),
            ExportColumn::make('document')
                ->label('Documento'),
            ExportColumn::make('phone')
                ->label('Telefone'),
            ExportColumn::make('address.state.name')
                ->label('Estado'),
            ExportColumn::make('address.city.name')
                ->label('Cidade'),
            ExportColumn::make('is_active')
                ->label('Status')
                ->formatStateUsing(fn(bool $state): string => $state ? 'Ativo' : 'Inativo'),
        ];
    }

    public static function configureUsing(Exporter $exporter): void
    {
        $exporter
            ->disk('local')
            ->fileName(fn () => 'clientes-' . now()->format('Y-m-d') . '.xlsx') 
            ->fileName(fn () => 'clientes-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function getQuery(): Builder
    {
        return User::query()->with(['address.state', 'address.city']);
    }

    public static function getFormSchema(): array
    {
        return [];
    }

    public function getHeading(): string
    {
        return 'Lista de Clientes';
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
        return 'Sua exportação de clientes foi concluída e está pronta para download.';
    }
}
