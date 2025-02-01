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
            // Usuários
            'view_users',
            'create_users', 
            'edit_users',
            'delete_users',
            
            // Produtos
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            
            // Pedidos
            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            
            // Financeiro
            'view_financial',
            'manage_financial',

            // Configurações
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Criar roles com suas permissões
        $roles = [
            'super-admin' => [
                'name' => 'Super Admin',
                'permissions' => ['*'] // Todas as permissões
            ],
            'tenant-admin' => [
                'name' => 'Admin',
                'permissions' => ['*'] // Todas as permissões dentro do tenant
            ],
            'manager' => [
                'name' => 'Manager',
                'permissions' => [
                    'view_users', 'create_users', 'edit_users', 'delete_users',
                    'view_products', 'create_products', 'edit_products', 'delete_products',
                    'view_orders', 'create_orders', 'edit_orders', 'delete_orders',
                    'manage_settings'
                    // Não tem acesso ao financeiro
                ]
            ],
            'operator' => [
                'name' => 'Operador',
                'permissions' => [
                    'view_products', 'edit_products',
                    'view_orders', 'create_orders', 'edit_orders'
                    // Não tem acesso ao financeiro nem às configurações
                ]
            ],
            'client' => [
                'name' => 'Client',
                'permissions' => [
                    'view_products',
                    'view_orders', 'create_orders'
                    // Acesso limitado apenas a produtos e seus próprios pedidos
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