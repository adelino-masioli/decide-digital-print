<?php

namespace App\Filament\Exports;

use App\Models\Opportunity;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Exports\Enums\ExportFormat;

class OpportunityExport extends Exporter
{
    protected static ?string $model = Opportunity::class;

    public function getFileName(Export $export): string
    {
        return "oportunidades-{$export->id}";
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('title')
                ->label('Projeto'),
            ExportColumn::make('client.name')
                ->label('Cliente'),
            ExportColumn::make('responsible.name')
                ->label('Responsável'),
            ExportColumn::make('value')
                ->label('Valor')
                ->formatStateUsing(fn ($state) => 'R$ ' . number_format($state, 2, ',', '.')),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'lead' => 'Primeiro Contato',
                    'negotiation' => 'Em Orçamento',
                    'proposal' => 'Orçamento Enviado',
                    'won' => 'Pedido Fechado',
                    'lost' => 'Não Aprovado',
                }),
            ExportColumn::make('expected_closure_date')
                ->label('Previsão de Fechamento')
                ->formatStateUsing(fn ($state) => $state ? date('d/m/Y', strtotime($state)) : ''),
            ExportColumn::make('description')
                ->label('Descrição'),
        ];
    }

    public function getQuery(): Builder
    {
        return Opportunity::query()->with(['client', 'responsible']);
    }

    public function getHeading(): string
    {
        return 'Lista de Oportunidades';
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
        return 'Sua exportação de oportunidades foi concluída e está pronta para download.';
    }
} 