<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Hash;

class RegisterTenant extends Register
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('last_name')
                            ->label('Sobrenome')
                            ->required()
                            ->maxLength(255),
                    ]),

                TextInput::make('document')
                    ->label('CPF/CNPJ')
                    ->required()
                    ->unique(User::class)
                    ->maxLength(20),

                TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->required(),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class)
                    ->maxLength(255),

                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->same('passwordConfirmation'),

                TextInput::make('passwordConfirmation')
                    ->label('Confirmar Senha')
                    ->password()
                    ->required()
                    ->minLength(8),
            ]);
    }

    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'document' => $data['document'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'is_tenant_admin' => true,
        ]);

        $user->assignRole('tenant_admin');

        return $user;
    }
} 