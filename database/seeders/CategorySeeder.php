<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Categorias para Gráfica A
        $graficaA = User::where('email', 'admin@graficaa.com')->first();
        
        $impressaoA = Category::create([
            'name' => 'Impressão Digital',
            'description' => 'Serviços de impressão digital',
            'tenant_id' => $graficaA->id,
        ]);

        Category::create([
            'name' => 'Pequeno Formato',
            'description' => 'Impressões em pequeno formato',
            'parent_id' => $impressaoA->id,
            'tenant_id' => $graficaA->id,
        ]);

        Category::create([
            'name' => 'Grande Formato',
            'description' => 'Impressões em grande formato',
            'parent_id' => $impressaoA->id,
            'tenant_id' => $graficaA->id,
        ]);

        // Categorias para Gráfica Rápida
        $graficaRapida = User::where('email', 'admin@graficarapida.com')->first();
        
        $impressaoR = Category::create([
            'name' => 'Offset',
            'description' => 'Impressão offset',
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Cartões',
            'description' => 'Cartões de visita',
            'parent_id' => $impressaoR->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Folders',
            'description' => 'Folders e panfletos',
            'parent_id' => $impressaoR->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Panfletos',
            'description' => 'Impressão de panfletos variados',
            'parent_id' => $impressaoR->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Papelaria Corporativa',
            'description' => 'Papel timbrado, envelopes, pastas',
            'parent_id' => $impressaoR->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        // Categoria de acabamento e personalização
        $acabamento = Category::create([
            'name' => 'Acabamento e Personalização',
            'description' => 'Serviços de acabamento gráfico',
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Plastificação',
            'description' => 'Plastificação de impressos',
            'parent_id' => $acabamento->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Verniz',
            'description' => 'Verniz localizado e total',
            'parent_id' => $acabamento->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Hot Stamping',
            'description' => 'Detalhes metálicos para impressos',
            'parent_id' => $acabamento->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Relevo',
            'description' => 'Efeitos de relevo em impressos',
            'parent_id' => $acabamento->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        // Categoria de materiais promocionais
        $materiaisPromocionais = Category::create([
            'name' => 'Materiais Promocionais',
            'description' => 'Materiais gráficos para divulgação',
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Banners',
            'description' => 'Banners de diversos tamanhos',
            'parent_id' => $materiaisPromocionais->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Adesivos',
            'description' => 'Adesivos personalizados',
            'parent_id' => $materiaisPromocionais->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Brindes Personalizados',
            'description' => 'Canecas, chaveiros, cadernos',
            'parent_id' => $materiaisPromocionais->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        // Categoria de encadernação
        $encadernacao = Category::create([
            'name' => 'Encadernação',
            'description' => 'Serviços de encadernação',
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Espiral',
            'description' => 'Encadernação em espiral',
            'parent_id' => $encadernacao->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Capa Dura',
            'description' => 'Encadernação de livros e teses',
            'parent_id' => $encadernacao->id,
            'tenant_id' => $graficaRapida->id,
        ]);

        Category::create([
            'name' => 'Wire-O',
            'description' => 'Encadernação profissional Wire-O',
            'parent_id' => $encadernacao->id,
            'tenant_id' => $graficaRapida->id,
        ]);
    }
}
