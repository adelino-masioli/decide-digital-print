<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class WelcomeModal extends Component
{
    public $showModal = false;

    public function mount()
    {
        $user = Auth::user();
        if ($user && !$user->welcome_confirmed_at) {
            $this->showModal = true;
        }
    }

    public function confirm()
    {
        $user = Auth::user();
        if ($user) {
            $user->welcome_confirmed_at = now();
            $user->save();
            // Opcional: armazene em sessão para evitar consulta repetida
            session()->put('welcome_confirmed', true);
            $this->showModal = false;
        }
    }

    public function close()
    {
        // Se o usuário fechar sem confirmar, considere registrar também a ação
        $user = Auth::user();
        if ($user) {
            $user->welcome_confirmed_at = now();
            $user->save();
            session()->put('welcome_confirmed', true);
        }
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.welcome-modal');
    }
}