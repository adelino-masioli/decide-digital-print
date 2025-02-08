<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterTenantController extends Controller
{
    public function create()
    {
        return view('auth.register-tenant');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'document' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'document' => $request->document,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_tenant_admin' => true,
        ]);

        $user->assignRole('tenant_admin');

        auth()->login($user);


        return redirect()->route('filament.admin.pages.dashboard');
    }
} 