<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permissões para Clientes
        Permission::create(['name' => 'client.list']);
        Permission::create(['name' => 'client.export']);
        Permission::create(['name' => 'client.create']);
        Permission::create(['name' => 'client.edit']);
        Permission::create(['name' => 'client.delete']);

        // Permissões para Produtos
        Permission::create(['name' => 'product.list']);
        Permission::create(['name' => 'product.export']);
        Permission::create(['name' => 'product.create']);
        Permission::create(['name' => 'product.edit']);
        Permission::create(['name' => 'product.delete']);

        // Permissões para Categorias
        Permission::create(['name' => 'category.list']);
        Permission::create(['name' => 'category.export']);
        Permission::create(['name' => 'category.create']);
        Permission::create(['name' => 'category.edit']);
        Permission::create(['name' => 'category.delete']);

        // Permissões para Orçamentos
        Permission::create(['name' => 'quote.list']);
        Permission::create(['name' => 'quote.export']);
        Permission::create(['name' => 'quote.create']);
        Permission::create(['name' => 'quote.edit']);
        Permission::create(['name' => 'quote.delete']);

        // Permissões para Pedidos
        Permission::create(['name' => 'order.list']);
        Permission::create(['name' => 'order.export']);
        Permission::create(['name' => 'order.create']);
        Permission::create(['name' => 'order.edit']);
        Permission::create(['name' => 'order.delete']);

        // Permissões para Quadro de Produção
        Permission::create(['name' => 'production.board.access']);

        // Permissões para Oportunidades
        Permission::create(['name' => 'opportunity.list']);
        Permission::create(['name' => 'opportunity.export']);
        Permission::create(['name' => 'opportunity.create']);
        Permission::create(['name' => 'opportunity.edit']);
        Permission::create(['name' => 'opportunity.delete']);

        // Atribuir todas as permissões ao papel de tenant-admin
        $adminRole = Role::where('name', 'tenant-admin')->first();
        
        $adminRole->givePermissionTo([
            'client.list', 'client.export', 'client.create', 'client.edit', 'client.delete',
            'product.list', 'product.export', 'product.create', 'product.edit', 'product.delete',
            'category.list', 'category.export', 'category.create', 'category.edit', 'category.delete',
            'quote.list', 'quote.export', 'quote.create', 'quote.edit', 'quote.delete',
            'order.list', 'order.export', 'order.create', 'order.edit', 'order.delete',
            'production.board.access',
            'opportunity.list', 'opportunity.export', 'opportunity.create', 'opportunity.edit', 'opportunity.delete',
        ]);
    }
} 