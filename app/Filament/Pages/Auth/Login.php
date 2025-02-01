<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';
    protected static string $layout = 'filament-panels::components.layout.base';

    public function mount(): void
    {
        parent::mount();
        
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->autocomplete()
                ->autofocus(),
            TextInput::make('password')
                ->label('Senha')
                ->password()
                ->required(),
            Checkbox::make('remember')
                ->label('Lembrar-me'),
            Placeholder::make('spacing')
                ->view('filament.components.spacing'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction()
                ->label('Login')
                ->color('warning')
                ->size('lg')
                ->extraAttributes([
                    'class' => 'w-full flex py-3 mt-10 font-bold text-black bg-yellow-500 hover:bg-yellow-600 rounded-md'
                ]),
        ];
    }

    public function getHeading(): Htmlable|string
    {
        return 'Bem-vindo de volta';
    }

    public function getSubheading(): Htmlable|string
    {
        return 'Faça login para acessar sua conta';
    }
} 