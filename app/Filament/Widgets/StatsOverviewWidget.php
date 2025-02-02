<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Opportunity;
use App\Models\Interaction;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = null;
    protected static ?int $maxHeight = 100; // Altura reduzida para os cards

    protected function getStats(): array
    {
        return [
            Stat::make('Total de Oportunidades', Opportunity::count())
                ->description('Total de oportunidades cadastradas')
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make('Oportunidades Abertas', Opportunity::where('status', 'open')->count())
                ->description('Oportunidades em negociação')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),

            Stat::make('Interações Recentes', Interaction::whereMonth('created_at', now()->month)->count())
                ->description('Interações no mês atual')
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('primary'),
        ];
    }
} 