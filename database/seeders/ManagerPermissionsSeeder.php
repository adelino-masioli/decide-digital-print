<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ManagerPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permissões para Clientes
        Permission::firstOrCreate(['name' => 'client.list']);
        Permission::firstOrCreate(['name' => 'client.export']);
        Permission::firstOrCreate(['name' => 'client.create']);
        Permission::firstOrCreate(['name' => 'client.edit']);
        Permission::firstOrCreate(['name' => 'client.delete']);

        // Permissões para Produtos
        Permission::firstOrCreate(['name' => 'product.list']);
        Permission::firstOrCreate(['name' => 'product.export']);
        Permission::firstOrCreate(['name' => 'product.create']);
        Permission::firstOrCreate(['name' => 'product.edit']);
        Permission::firstOrCreate(['name' => 'product.delete']);

        // Permissões para Categorias
        Permission::firstOrCreate(['name' => 'category.list']);
        Permission::firstOrCreate(['name' => 'category.export']);
        Permission::firstOrCreate(['name' => 'category.create']);
        Permission::firstOrCreate(['name' => 'category.edit']);
        Permission::firstOrCreate(['name' => 'category.delete']);

        // Permissões para Orçamentos
        Permission::firstOrCreate(['name' => 'quote.list']);
        Permission::firstOrCreate(['name' => 'quote.export']);
        Permission::firstOrCreate(['name' => 'quote.create']);
        Permission::firstOrCreate(['name' => 'quote.edit']);
        Permission::firstOrCreate(['name' => 'quote.delete']);

        // Permissões para Pedidos
        Permission::firstOrCreate(['name' => 'order.list']);
        Permission::firstOrCreate(['name' => 'order.export']);
        Permission::firstOrCreate(['name' => 'order.create']);
        Permission::firstOrCreate(['name' => 'order.edit']);
        Permission::firstOrCreate(['name' => 'order.delete']);

        // Permissões para Quadro de Produção
        Permission::firstOrCreate(['name' => 'production.board.access']);

        // Permissões para Oportunidades
        Permission::firstOrCreate(['name' => 'opportunity.list']);
        Permission::firstOrCreate(['name' => 'opportunity.export']);
        Permission::firstOrCreate(['name' => 'opportunity.create']);
        Permission::firstOrCreate(['name' => 'opportunity.edit']);
        Permission::firstOrCreate(['name' => 'opportunity.delete']);

        // Atribuir todas as permissões ao papel de manager
        $managerRole = Role::where('name', 'manager')->first();
        
        $managerRole->givePermissionTo([
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