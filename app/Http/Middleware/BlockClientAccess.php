<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BlockClientAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->hasRole('client')) {
            Auth::logout();
            
            // Limpa a sessão
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Redireciona para a página de login do Filament com uma mensagem
            return redirect()->route('filament.admin.auth.login')->with('error', 'Acesso não autorizado.');
        }

        return $next($request);
    }
} 