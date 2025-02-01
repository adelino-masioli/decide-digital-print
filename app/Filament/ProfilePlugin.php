<?php

namespace App\Filament;

use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Panel;

class ProfilePlugin implements Plugin
{
    public function getId(): string
    {
        return 'profile';
    }

    public function register(Panel $panel): void
    {
        $panel->userMenuItems([
            'profile' => MenuItem::make()
                ->label('Meu Perfil')
                ->url(fn () => route('filament.admin.resources.users.edit', ['record' => auth()->id()]))
                ->icon('heroicon-o-user')
                ->order(1),
        ]);

        $panel->navigationItems([
            'profile' => NavigationItem::make()
                ->label('Meu Perfil')
                ->icon('heroicon-o-user-circle')
                ->url(fn () => route('filament.admin.resources.users.edit', ['record' => auth()->id()]))
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.users.edit') && request()->route('record') == auth()->id())
                ->group('Configurações')
                ->sort(2)
                ->visible(fn () => !auth()->user()->hasRole(['super-admin', 'tenant-admin']))
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
} 