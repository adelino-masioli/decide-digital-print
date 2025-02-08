<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view_users',
            'view_products',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Criar roles com suas permissões
        $roles = [
            'super-admin' => [
                'name' => 'Super Admin',
                'permissions' => ['view_users'] 
            ],
            'tenant-admin' => [
                'name' => 'Admin',
                'permissions' => ['view_products']
            ],
            'manager' => [
                'name' => 'Manager',
                'permissions' => ['view_products']
            ],
            'operator' => [
                'name' => 'Operador',
                'permissions' => [
                    'view_products'
                ]
            ],
            'client' => [
                'name' => 'Client',
                'permissions' => [
                    'view_products'
                ]
            ]
        ];

        foreach ($roles as $roleKey => $config) {
            $role = Role::create(['name' => $roleKey]);
            
            if ($config['permissions'] === ['*']) {
                $role->givePermissionTo(Permission::all());
            } else {
                $role->givePermissionTo($config['permissions']);
            }
        }
    }
} 