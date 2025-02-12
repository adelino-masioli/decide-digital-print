<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WelcomeModalWidget extends Widget
{
    protected static string $view = 'filament.widgets.welcome-modal-widget';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        return [
            'showModal' => Auth::check() && is_null(Auth::user()->welcome_confirmed_at),
        ];
    }
}

