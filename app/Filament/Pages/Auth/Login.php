<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Forms\Components\Component;


class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';
    protected static string $layout = 'filament-panels::components.layout.base';

    public function mount(): void
    {
        parent::mount();

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data')
            ->extraAttributes(['style'=>'gap:0.5rem']);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->placeholder('Digite seu email')
            ->extraInputAttributes([
                'class' => 'w-full py-3 mt-0 text-black rounded-md',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return  TextInput::make('password')
            ->label('Senha')
            ->password()
            ->required()
            ->placeholder('Digite sua senha')
            ->extraInputAttributes([
                'class' => 'w-full py-3  mt-0 text-black rounded-md',
            ]);
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Lembrar-me')
            ->extraInputAttributes([
                'class' => 'text-black  mt-0',
            ]);
    }

    protected function getSpacingFormComponent(): Component
    {
        return  Placeholder::make('spacing')
            ->view('filament.components.spacing');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getAuthenticateFormAction()
                ->label('Login')
                ->color('primary')
                ->size('lg')
                ->extraAttributes([
                    'class' => 'w-full flex py-4 mt-0  rounded-md',
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
