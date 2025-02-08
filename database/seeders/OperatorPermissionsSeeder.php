<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class OperatorPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Permissões para Clientes
        Permission::firstOrCreate(['name' => 'client.list']);
        Permission::firstOrCreate(['name' => 'client.create']);
        Permission::firstOrCreate(['name' => 'client.edit']);

        // Permissões para Produtos
        Permission::firstOrCreate(['name' => 'product.list']);
        Permission::firstOrCreate(['name' => 'product.view']);

        // Permissões para Orçamentos
        Permission::firstOrCreate(['name' => 'quote.list']);
        Permission::firstOrCreate(['name' => 'quote.create']);
        Permission::firstOrCreate(['name' => 'quote.edit']);

        // Permissões para Pedidos
        Permission::firstOrCreate(['name' => 'order.list']);
        Permission::firstOrCreate(['name' => 'order.create']);
        Permission::firstOrCreate(['name' => 'order.edit']);

        // Permissões para Quadro de Produção
        Permission::firstOrCreate(['name' => 'production.board.access']);

        // Permissões para Oportunidades
        Permission::firstOrCreate(['name' => 'opportunity.list']);
        Permission::firstOrCreate(['name' => 'opportunity.create']);
        Permission::firstOrCreate(['name' => 'opportunity.edit']);

        // Atribuir permissões ao papel de operator
        $operatorRole = Role::where('name', 'operator')->first();
        
        $operatorRole->givePermissionTo([
            'client.list', 'client.create', 'client.edit',
            'product.list', 'product.view',
            'quote.list', 'quote.create', 'quote.edit',
            'order.list', 'order.create', 'order.edit',
            'production.board.access',
            'opportunity.list', 'opportunity.create', 'opportunity.edit',
        ]);
    }
} 