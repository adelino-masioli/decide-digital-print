<?php

namespace App\Providers;

use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Illuminate\Support\ServiceProvider;
use Filament\Panel;

class FilamentServiceProvider extends ServiceProvider
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
    }
} 