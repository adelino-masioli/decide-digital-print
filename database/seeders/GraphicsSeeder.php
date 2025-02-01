<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GraphicsSeeder extends Seeder
{
    public function run(): void
    {
        // Gráfica Rápida
        $adminGraficaRapida = User::create([
            'name' => 'Gráfica',
            'last_name' => 'Rápida',
            'email' => 'admin@graficarapida.com',
            'document' => '55555555555',
            'phone' => '(55) 55555-5555',
            'password' => Hash::make('password'),
            'tenant_id' => null,
            'is_active' => true,
            'is_tenant_admin' => true,
        ]);
        $adminGraficaRapida->update(['tenant_id' => $adminGraficaRapida->id]);
        $adminGraficaRapida->assignRole('tenant-admin');

        // Manager da Gráfica Rápida
        User::create([
            'name' => 'Manager',
            'last_name' => 'Gráfica Rápida',
            'email' => 'manager@graficarapida.com',
            'document' => '66666666666',
            'phone' => '(66) 66666-6666',
            'password' => Hash::make('password'),
            'tenant_id' => $adminGraficaRapida->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('manager');

        // Operador da Gráfica Rápida
        User::create([
            'name' => 'Operador',
            'last_name' => 'Gráfica Rápida',
            'email' => 'operador@graficarapida.com',
            'document' => '77777777777',
            'phone' => '(77) 77777-7777',
            'password' => Hash::make('password'),
            'tenant_id' => $adminGraficaRapida->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('operator');

        // Cliente da Gráfica Rápida
        User::create([
            'name' => 'Cliente',
            'last_name' => 'Gráfica Rápida',
            'email' => 'cliente@graficarapida.com',
            'document' => '88888888888',
            'phone' => '(88) 88888-8888',
            'password' => Hash::make('password'),
            'tenant_id' => $adminGraficaRapida->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('client');

        // Gráfica Express
        $adminGraficaExpress = User::create([
            'name' => 'Gráfica',
            'last_name' => 'Express',
            'email' => 'admin@graficaexpress.com',
            'document' => '99999999999',
            'phone' => '(99) 99999-9999',
            'password' => Hash::make('password'),
            'tenant_id' => null,
            'is_active' => true,
            'is_tenant_admin' => true,
        ]);
        $adminGraficaExpress->update(['tenant_id' => $adminGraficaExpress->id]);
        $adminGraficaExpress->assignRole('tenant-admin');

        // Manager da Gráfica Express
        User::create([
            'name' => 'Manager',
            'last_name' => 'Gráfica Express',
            'email' => 'manager@graficaexpress.com',
            'document' => '12121212121',
            'phone' => '(12) 12121-2121',
            'password' => Hash::make('password'),
            'tenant_id' => $adminGraficaExpress->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('manager');

        // Operador da Gráfica Express
        User::create([
            'name' => 'Operador',
            'last_name' => 'Gráfica Express',
            'email' => 'operador@graficaexpress.com',
            'document' => '13131313131',
            'phone' => '(13) 13131-3131',
            'password' => Hash::make('password'),
            'tenant_id' => $adminGraficaExpress->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('operator');

        // Cliente da Gráfica Express
        User::create([
            'name' => 'Cliente',
            'last_name' => 'Gráfica Express',
            'email' => 'cliente@graficaexpress.com',
            'document' => '14141414141',
            'phone' => '(14) 14141-4141',
            'password' => Hash::make('password'),
            'tenant_id' => $adminGraficaExpress->id,
            'is_active' => true,
            'is_tenant_admin' => false,
        ])->assignRole('client');
    }
} 