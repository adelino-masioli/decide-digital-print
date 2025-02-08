<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permissões para Clientes
        Permission::firstOrCreate(['name' => 'user.list']);
        Permission::firstOrCreate(['name' => 'user.export']);
        Permission::firstOrCreate(['name' => 'user.create']);
        Permission::firstOrCreate(['name' => 'user.edit']);
        Permission::firstOrCreate(['name' => 'user.delete']);

        Permission::firstOrCreate(['name' => 'role.list']);
        Permission::firstOrCreate(['name' => 'role.export']);
        Permission::firstOrCreate(['name' => 'role.create']);
        Permission::firstOrCreate(['name' => 'role.edit']);
        Permission::firstOrCreate(['name' => 'role.delete']);   

        Permission::firstOrCreate(['name' => 'permission.list']);
        Permission::firstOrCreate(['name' => 'permission.export']);
        Permission::firstOrCreate(['name' => 'permission.create']);
        Permission::firstOrCreate(['name' => 'permission.edit']);
        Permission::firstOrCreate(['name' => 'permission.delete']);

        // Atribuir todas as permissões ao papel de manager
        $superAdminRole = Role::where('name', 'super-admin')->first();
        
        $superAdminRole->givePermissionTo([
            'user.list', 'user.export', 'user.create', 'user.edit', 'user.delete',
            'role.list', 'role.export', 'role.create', 'role.edit', 'role.delete',
            'permission.list', 'permission.export', 'permission.create', 'permission.edit', 'permission.delete',
        ]);
    }
} 