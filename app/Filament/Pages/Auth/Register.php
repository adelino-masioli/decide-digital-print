<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Component;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Filament\Http\Responses\Auth\RegistrationResponse as FilamentRegistrationResponse;

class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';
    protected static string $layout = 'filament-panels::components.layout.base';

    public function mount(): void
    {
        parent::mount();

        $this->form->fill();

        if (session()->has('error')) {
            $this->addError('data', session('error'));
        }
    }

    protected function getRegistrationResponse(User $user): RegistrationResponse
    {
        return app(FilamentRegistrationResponse::class);
    }

    public function register(): ?RegistrationResponse
    {
        $this->form->validate();

        try {
            $data = $this->form->getState();

            $user = User::create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
                'is_tenant_admin' => true,
                'tenant_id' => null, // Inicialmente null
            ]);

            // Set tenant_id as the user's own id
            $user->update(['tenant_id' => $user->id]);

            // Assign tenant-admin role
            $user->assignRole('tenant-admin');

            event(new Registered($user));

            Auth::login($user);

            session()->regenerate();

            Notification::make()
                ->title('Bem-vindo!')
                ->success()
                ->send();

            return $this->getRegistrationResponse($user);
        } catch (\Exception $e) {
            Log::error('Erro no registro de usuário: ' . $e->getMessage(), [
                'error' => $e,
                'trace' => $e->getTraceAsString(),
                'data' => $this->form->getState()
            ]);

            Notification::make()
                ->title('Erro ao criar conta')
                ->danger()
                ->body('Erro: ' . $e->getMessage())
                ->send();

            return null;
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getLastNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ])
            ->statePath('data')
            ->extraAttributes(['style'=>'gap:0.5rem']);
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('Nome')
            ->required()
            ->maxLength(255)
            ->autocomplete()
            ->autofocus()
            ->placeholder('Digite seu primeiro nome')
            ->extraInputAttributes([
                'class' => 'w-full py-3 mt-0 text-black rounded-md',
            ]);
    }

    protected function getLastNameFormComponent(): Component
    {
        return TextInput::make('last_name')
            ->label('Sobrenome')
            ->required()
            ->maxLength(255)
            ->autocomplete()
            ->placeholder('Digite seu sobrenome')
            ->extraInputAttributes([
                'class' => 'w-full py-3 mt-0 text-black rounded-md',
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Email')
            ->email()
            ->required()
            ->maxLength(255)
            ->autocomplete()
            ->placeholder('Digite seu email')
            ->extraInputAttributes([
                'class' => 'w-full py-3 mt-0 text-black rounded-md',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Senha')
            ->password()
            ->required()
            ->minLength(8)
            ->placeholder('Digite sua senha')
            ->extraInputAttributes([
                'class' => 'w-full py-3 mt-0 text-black rounded-md',
            ]);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('password_confirmation')
            ->label('Confirmar Senha')
            ->password()
            ->required()
            ->minLength(8)
            ->placeholder('Confirme sua senha')
            ->extraInputAttributes([
                'class' => 'w-full py-3 mt-0 text-black rounded-md',
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            $this->getRegisterFormAction()
                ->label('Registrar')
                ->color('primary')
                ->size('lg')
                ->extraAttributes([
                    'class' => 'w-full flex py-4 pt-10 rounded-md',
                ]),
        ];
    }

    public function getHeading(): Htmlable|string
    {
        return 'Decide Digital - Print 👋';
    }

    public function getSubheading(): Htmlable|string
    {
        return 'Crie sua conta e comece a gerenciar seus projetos.';
    }
} 