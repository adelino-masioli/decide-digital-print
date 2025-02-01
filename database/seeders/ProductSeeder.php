<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Produtos para Gráfica Rápida
        $graficaRapida = User::where('email', 'admin@graficarapida.com')->first();
        $categoriaOffset = Category::where('tenant_id', $graficaRapida->id)
            ->where('name', 'Offset')
            ->first();

        Product::create([
            'name' => 'Cartão de Visita Premium',
            'description' => 'Cartão de visita em papel couché 300g com laminação fosca',
            'category_id' => $categoriaOffset->id,
            'tenant_id' => $graficaRapida->id,
            'format' => '9x5cm',
            'material' => 'Couché 300g',
            'weight' => '300g',
            'finishing' => 'Laminação Fosca',
            'color' => '4x4',
            'production_time' => 3,
            'min_quantity' => 100,
            'max_quantity' => 10000,
            'base_price' => 90.00,
            'customization_options' => [
                'Verniz UV localizado',
                'Hot Stamping',
                'Cantos arredondados'
            ],
            'file_requirements' => [
                'format' => 'PDF',
                'color_mode' => 'CMYK',
                'resolution' => '300 DPI'
            ],
        ]);

        // Adicione mais produtos conforme necessário...
    }
} 