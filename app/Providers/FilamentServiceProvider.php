<?php

namespace App\Providers;

use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Illuminate\Support\ServiceProvider;
use Filament\Panel;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Assets\Js;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\OpportunitiesChart;
use App\Filament\Widgets\LatestOpportunities;

class FilamentServiceProvider extends PanelProvider
{
    public function boot(): void
    {
        // Configurar o tenant atual baseado no usuário logado
        if (auth()->check()) {
            $user = auth()->user();
            app()->bind('current.tenant', fn() => $user->getTenantId());
        }

        // Configurar o painel do Filament
        Panel::configureUsing(function (Panel $panel): void {
            $panel
                ->defaultLocale('pt_BR')
                ->locale('pt_BR');

            // Adiciona item no menu do usuário (dropdown no canto superior direito)
            $panel->userMenuItems([
                MenuItem::make()
                    ->label('Meu Perfil')
                    ->url(fn () => route('filament.admin.resources.users.edit', ['record' => auth()->id()]))
                    ->icon('heroicon-o-user'),
            ]);

            // Opcional: Adiciona link rápido no dashboard
            $panel->navigationItems([
                NavigationItem::make('Meu Perfil')
                    ->url(fn () => route('filament.admin.resources.users.edit', ['record' => auth()->id()]))
                    ->icon('heroicon-o-user')
                    ->group('Acesso Rápido')
                    ->sort(1),
            ]);
        });

        // Registra o arquivo de tradução do date picker
        FilamentAsset::register([
            Js::make('date-picker-pt-BR', __DIR__ . '/../../resources/js/date-picker-pt-BR.js')
                ->loadedOnRequest(),
        ]);
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->widgets([
                StatsOverviewWidget::class,
                OpportunitiesChart::class,
                LatestOpportunities::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
} 