<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\OpportunitiesChart;
use App\Filament\Widgets\InteractionsTypeChart;
use App\Filament\Widgets\LatestOpportunities;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            OpportunitiesChart::class,
            InteractionsTypeChart::class,
            StatsOverviewWidget::class,
            LatestOpportunities::class,
        ];
    }
} 