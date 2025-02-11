<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\State;

class RegisterTenantController extends Controller
{
    public function create()
    {
        $states = State::all();
        return view('auth.register-tenant', compact('states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Basic Information
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'document' => ['required', 'string', 'max:20', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()],
            
            // Additional Information
            'company_name' => ['required', 'string', 'max:255'],
            'trading_name' => ['nullable', 'string', 'max:255'],
            'state_registration' => ['nullable', 'string', 'max:20'],
            'municipal_registration' => ['nullable', 'string', 'max:20'],
            'company_address' => ['required', 'string', 'max:255'],
            'company_latitude' => ['nullable', 'numeric'],
            'company_longitude' => ['nullable', 'numeric'],
            
            // Address Information
            'zip_code' => ['required', 'string', 'max:9'],
            'street' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:20'],
            'neighborhood' => ['required', 'string', 'max:255'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'complement' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'document' => $request->document,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_tenant_admin' => true,
            'is_active' => true,
            'company_name' => $request->company_name,
            'trading_name' => $request->trading_name,
            'state_registration' => $request->state_registration,
            'municipal_registration' => $request->municipal_registration,
            'company_address' => $request->company_address,
            'company_latitude' => $request->company_latitude,
            'company_longitude' => $request->company_longitude,
        ]);

        // Set tenant_id as the user's own id
        $user->update(['tenant_id' => $user->id]);
        
        // Create address
        $user->address()->create([
            'zip_code' => $request->zip_code,
            'street' => $request->street,
            'number' => $request->number,
            'neighborhood' => $request->neighborhood,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'complement' => $request->complement,
        ]);

        // Assign tenant-admin role
        $user->assignRole('tenant-admin');

        auth()->login($user);

        return redirect()->route('filament.admin.pages.dashboard');
    }
} 