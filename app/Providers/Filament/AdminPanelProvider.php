<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard as FilamentDashboard;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\ProductionBoard;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\OpportunitiesChart;
use App\Filament\Widgets\LatestOpportunities;
use App\Filament\Pages\Auth\Login as FilamentLogin;
use \App\Http\Middleware\BlockClientAccess;
use Illuminate\Support\Facades\Blade;
use Filament\Navigation\NavigationGroup;
use Filament\Enums\ThemeMode;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->defaultThemeMode(ThemeMode::Light)
            ->login(FilamentLogin::class)
            ->registration(Register::class)
            ->colors([
                'primary' => Color::Blue,
                'secondary' => Color::Slate,
                'success' => Color::Green,
                'warning' => Color::Orange,
                'danger' => Color::Red,
                'info' => Color::Sky,
                'gray' => Color::Gray
            ])
            ->navigationGroups([
                'Catálogo',
                'Administração',
                'Vendas',
                'CRM',
                'Minha Conta',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
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
                BlockClientAccess::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                'panels::head.end',
                fn () => '
                <style>
                    /* Estilo para o título centralizado */
                    .fi-topbar .fi-topbar-content {
                        position: relative;
                    }
                    
                    #decide-digital-title {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        font-weight: bold;
                        font-size: 1.25rem;
                        color: var(--primary-600);
                        display: none;
                        z-index: 50;
                    }
                    
                    /* Mostrar o título quando a sidebar estiver recolhida */
                    .fi-sidebar-collapsed #decide-digital-title,
                    [data-sidebar-collapsed="true"] ~ * #decide-digital-title,
                    .fi-sidebar[aria-expanded="false"] ~ * #decide-digital-title {
                        display: block;
                    }
                </style>
                '
            )
            ->renderHook(
                'panels::topbar.start',
                fn () => '<div id="decide-digital-title"><a href="/admin" title="Decide Digital - Print">Decide Digital - Print</a></div>'
            )
            ->renderHook(
                'panels::body.end',
                fn () => view('components.welcome-modal') . '
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        // Verificar se o elemento existe
                        var title = document.getElementById("decide-digital-title");
                        if (!title) {
                            return;
                        }
                        
                        // Função para verificar o estado da sidebar
                        function checkSidebarState() {
                            // Verificar diferentes maneiras que o Filament pode marcar a sidebar como recolhida
                            var sidebar = document.querySelector(".fi-sidebar");
                            if (!sidebar) {
                                return;
                            }
                            
                            var isCollapsed = false;
                            
                            // Verificar atributo aria-expanded
                            if (sidebar.getAttribute("aria-expanded") === "false") {
                                isCollapsed = true;
                            }
                            
                            // Verificar atributo data-sidebar-collapsed
                            var dataSidebarCollapsed = document.querySelector("[data-sidebar-collapsed=\'true\']");
                            if (dataSidebarCollapsed) {
                                isCollapsed = true;
                            }
                            
                            // Verificar classe
                            var sidebarCollapsedElement = document.querySelector(".fi-sidebar-collapsed");
                            if (sidebarCollapsedElement) {
                                isCollapsed = true;
                            }
                            
                            // Verificar largura da sidebar como último recurso
                            var sidebarWidth = sidebar.offsetWidth;
                            if (sidebarWidth < 100) {
                                isCollapsed = true;
                            }
                            
                            if (isCollapsed) {
                                title.style.display = "block";
                            } else {
                                title.style.display = "none";
                            }
                        }
                        
                        // Verificar estado inicial após um pequeno delay para garantir que tudo foi carregado
                        setTimeout(checkSidebarState, 10);
                        
                        // Observar mudanças na sidebar
                        var sidebar = document.querySelector(".fi-sidebar");
                        if (sidebar) {
                            var observer = new MutationObserver(function(mutations) {
                                checkSidebarState();
                            });
                            
                            observer.observe(sidebar, { 
                                attributes: true, 
                                attributeFilter: ["class", "aria-expanded", "data-sidebar-collapsed"],
                                subtree: false
                            });
                        }
                        
                        // Adicionar listener para cliques no botão de toggle da sidebar
                        document.addEventListener("click", function(e) {
                            var toggleButton = e.target.closest("[data-collapse-sidebar-button]");
                            if (toggleButton) {
                                setTimeout(checkSidebarState, 200);
                            }
                        });
                        
                        // Verificar periodicamente (como fallback)
                        setInterval(checkSidebarState, 200);
                    });
                </script>
                '
            );
    }
}
