<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as FilamentDashboard;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\OpportunitiesChart;
use App\Filament\Widgets\InteractionsTypeChart;
use App\Filament\Widgets\LatestOpportunities;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\NavigationItem;
use Spatie\Permission\Models\Role;

class Dashboard extends FilamentDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static bool $shouldRegisterNavigation = false;

    public function boot(): void
    {
        $user = Auth::user();
        if (!$user) return;

        $isSuperAdmin = Role::where('name', 'super-admin')
            ->whereHas('users', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->exists();

        if ($isSuperAdmin) {
            $this->redirect('/admin/users');
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        return !Role::where('name', 'super-admin')
            ->whereHas('users', function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->exists();
    }

    public function getWidgets(): array
    {
        return [
            OpportunitiesChart::class,
            InteractionsTypeChart::class,
            StatsOverviewWidget::class,
            LatestOpportunities::class,
        ];
    }

    public static function getNavigationItems(): array
    {
        if (Auth::user()?->roles()->where('name', 'super-admin')->exists()) {
            return [];
        }

        return [
            NavigationItem::make()
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteName()))
                ->label(static::getNavigationLabel())
                ->badge(static::getNavigationBadge(), static::getNavigationBadgeColor())
                ->sort(static::getNavigationSort())
                ->group(static::getNavigationGroup())
                ->url(static::getNavigationUrl()),
        ];
    }
} 