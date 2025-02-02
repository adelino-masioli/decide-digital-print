<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Opportunity;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class OpportunitiesChart extends ChartWidget
{
    protected static ?string $heading = 'Oportunidades por Mês';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = [
        'sm' => 'full',
        'lg' => 1,
    ];
    protected static ?string $pollingInterval = null;
    
    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = Trend::model(Opportunity::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Oportunidades criadas',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                ],
            ],
            'labels' => $data->map(function (TrendValue $value) {
                return Carbon::parse($value->date)->translatedFormat('M');
            })->toArray(),
        ];
    }
} 