<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Interaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InteractionsTypeChart extends ChartWidget
{
    protected static ?string $heading = 'Interações por Tipo';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'lg' => 1, // Metade da largura em telas grandes
    ];
    
    protected static ?string $pollingInterval = null; // Desabilita o polling
    
    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $data = Interaction::query()
            ->select('type', DB::raw('count(*) as total'))
            ->whereMonth('created_at', now()->month)
            ->groupBy('type')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Quantidade de Interações',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                    ],
                ],
            ],
            'labels' => $data->pluck('type')->map(function ($type) {
                return match ($type) {
                    'call' => 'Ligação',
                    'email' => 'E-mail',
                    'meeting' => 'Reunião',
                    'other' => 'Outro',
                    default => ucfirst($type),
                };
            })->toArray(),
        ];
    }
} 