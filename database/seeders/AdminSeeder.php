<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super@admin.com',
            'document' => '00000000000',
            'phone' => '(00) 00000-0000',
            'password' => Hash::make('password'),
            'tenant_id' => null,
            'is_active' => true,
            'is_tenant_admin' => false,
        ]);

        $superAdmin->assignRole('super-admin');

        // Tenant 1 (Gráfica A)
        $tenant1Admin = User::create([
            'name' => 'Gráfica A',
            'last_name' => 'Admin',
            'email' => 'admin@graficaa.com',
            'document' => '11111111111',
            'phone' => '(11) 11111-1111',
            'password' => Hash::make('password'),
            'tenant_id' => null,
            'is_active' => true,
            'is_tenant_admin' => true,
        ]);
        $tenant1Admin->update(['tenant_id' => $tenant1Admin->id]);

        $tenant1Admin->assignRole('tenant-admin');

        // Criar dados iniciais para o tenant
        //$this->call(TenantSeeder::class, false, ['tenantId' => $tenant1Admin->id]);

        // Manager da Gráfica A
        User::create([
            'name' => 'Manager',
            'last_name' => 'Gráfica A',
            'email' => 'manager@graficaa.com',
            'document' => '22222222222',
            'phone' => '(22) 22222-2222',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant1Admin->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('manager');

        // Operador da Gráfica A
        User::create([
            'name' => 'Operador',
            'last_name' => 'Gráfica A',
            'email' => 'operador@graficaa.com',
            'document' => '33333333333',
            'phone' => '(33) 33333-3333',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant1Admin->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('operator');

        // Cliente da Gráfica A
        User::create([
            'name' => 'Cliente',
            'last_name' => 'Gráfica A',
            'email' => 'cliente@graficaa.com',
            'document' => '44444444444',
            'phone' => '(44) 44444-4444',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant1Admin->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('client');
    }
} 