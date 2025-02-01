<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssignUserRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Se o usuário ainda não tem role, atribui com base no is_tenant_admin
            if ($user->roles->isEmpty()) {
                if ($user->is_tenant_admin) {
                    $user->assignRole('tenant_admin');
                } else {
                    // Verifica se o usuário tem um tenant_id
                    if ($user->tenant_id) {
                        // Por padrão, atribui a role 'operator'
                        // O admin pode mudar depois para manager ou client
                        $user->assignRole('operator');
                    }
                }
            }
        }

        return $next($request);
    }
} 