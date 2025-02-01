<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('admin/login');
        }

        $user = auth()->user();
        $requestedTenantId = $request->route('tenant_id') ?? $request->input('tenant_id');

        if ($requestedTenantId && !$user->isTenantAdmin() && $user->getTenantId() !== (int) $requestedTenantId) {
            abort(403, 'Você não tem permissão para acessar este ambiente.');
        }

        return $next($request);
    }
} 