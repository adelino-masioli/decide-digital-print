<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Supply;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
    public function run(int $tenantId): void
    {
        Log::info('Starting TenantSeeder', ['tenant_id' => $tenantId]);

        try {
            DB::beginTransaction();

            // Categorias principais
            $categories = [
                'Impressão Digital' => [
                    'description' => 'Serviços de impressão digital em diversos materiais',
                    'subcategories' => [
                        'Pequenos Formatos' => 'Impressões em papel até A3',
                        'Grandes Formatos' => 'Banners, lonas e adesivos',
                        'Materiais Promocionais' => 'Folders, panfletos e cartões',
                        'Impressão em Textiles' => 'Impressão em tecidos e roupas',
                        'Impressão 3D' => 'Modelos e protótipos em impressão 3D',
                    ]
                ],
                'Acabamento' => [
                    'description' => 'Serviços de acabamento para impressos',
                    'subcategories' => [
                        'Encadernação' => 'Diversos tipos de encadernação',
                        'Laminação' => 'Acabamento com laminação fosca ou brilho',
                        'Corte e Vinco' => 'Serviços de corte especial',
                        'Refile' => 'Corte preciso para ajuste de tamanho',
                        'Dobras' => 'Dobra de folhetos, panfletos e outros materiais',
                        'Perfuração' => 'Perfuração de materiais para encaixe ou organização',
                    ]
                ],
                'Papelaria' => [
                    'description' => 'Materiais de papelaria personalizados',
                    'subcategories' => [
                        'Cartões de Visita' => 'Cartões de visita profissionais',
                        'Timbrados' => 'Papéis timbrados e envelopes',
                        'Blocos' => 'Blocos de anotações personalizados',
                        'Calendários' => 'Calendários personalizados em diversos formatos',
                        'Cadernos' => 'Cadernos personalizados com logotipos ou design exclusivo',
                    ]
                ],
                'Sinalização' => [
                    'description' => 'Impressão e criação de sinais e orientações',
                    'subcategories' => [
                        'Placas' => 'Placas de sinalização interna e externa',
                        'Etiquetas' => 'Etiquetas adesivas para diversos usos',
                        'Totens' => 'Totens e displays de apresentação',
                        'Faixas e Bandeiras' => 'Faixas para eventos e publicidade externa',
                    ]
                ],
                'Produtos Personalizados' => [
                    'description' => 'Impressão em objetos e produtos personalizados',
                    'subcategories' => [
                        'Canecas' => 'Canecas personalizadas com logo ou design exclusivo',
                        'Camisetas' => 'Impressão em camisetas e outros vestuários',
                        'Bolsas e Mochilas' => 'Personalização de bolsas e mochilas promocionais',
                        'Chaveiros' => 'Chaveiros personalizados com logotipo ou design exclusivo',
                    ]
                ],
                'Publicações e Editoração' => [
                    'description' => 'Serviços para criação e impressão de publicações e materiais editoriais',
                    'subcategories' => [
                        'Revistas' => 'Impressão de revistas e periódicos',
                        'Livros' => 'Impressão de livros e brochuras',
                        'Jornais' => 'Impressão de jornais personalizados',
                        'Catálogos' => 'Impressão de catálogos e materiais de vendas',
                    ]
                ],
                'Serviços Digitais' => [
                    'description' => 'Serviços complementares para a impressão digital',
                    'subcategories' => [
                        'Design Gráfico' => 'Criação de projetos gráficos para impressos',
                        'Edição de Imagem' => 'Ajustes e edição de imagens para impressão',
                        'Desenvolvimento de Layout' => 'Criação de layouts para publicações e materiais promocionais',
                        'Pré-Impressão' => 'Revisão e ajuste final de arquivos antes da impressão',
                    ]
                ],
            ];
            

            Log::info('Creating categories');
            
            // Criar categorias e subcategorias
            $categoryIds = [];
            foreach ($categories as $categoryName => $categoryData) {
                $category = Category::create([
                    'name' => $categoryName,
                    'description' => $categoryData['description'],
                    'tenant_id' => $tenantId,
                    'is_active' => true,
                ]);

                $categoryIds[$categoryName] = [
                    'id' => $category->id,
                    'subcategories' => [],
                ];

                foreach ($categoryData['subcategories'] as $subName => $subDescription) {
                    $subcategory = Category::create([
                        'name' => $subName,
                        'description' => $subDescription,
                        'parent_id' => $category->id,
                        'tenant_id' => $tenantId,
                        'is_active' => true,
                    ]);
                    
                    $categoryIds[$categoryName]['subcategories'][$subName] = $subcategory->id;
                }
            }

            Log::info('Creating suppliers');

            // Verifica se os estados existem antes de criar os fornecedores
            $this->call([
                StateSeeder::class,
                CitySeeder::class,
            ]);

            // Busca os IDs corretos das cidades
            $saopaulo_id = DB::table('cities')->where('city_id', 50308)->value('id');
            $rio_id = DB::table('cities')->where('city_id', 4557)->value('id');
            $curitiba_id = DB::table('cities')->where('city_id', 6902)->value('id');
            $portoalegre_id = DB::table('cities')->where('city_id', 14902)->value('id');
            $osasco_id = DB::table('cities')->where('city_id', 34401)->value('id');

            // Fornecedores
            $suppliers = [
                [
                    'name' => 'Distribuidora de Papéis ABC',
                    'email' => 'contato@papeis-abc.com',
                    'phone' => '(11) 3333-4444',
                    'postal_code' => '01234-567',
                    'address' => 'Rua dos Papéis, 123',
                    'neighborhood' => 'Centro',
                    'state_id' => 35, // São Paulo
                    'city_id' => $saopaulo_id,
                ],
                [
                    'name' => 'Suprimentos Gráficos XYZ',
                    'email' => 'vendas@xyz-suprimentos.com',
                    'phone' => '(11) 4444-5555',
                    'postal_code' => '04567-890',
                    'address' => 'Avenida das Tintas, 456',
                    'neighborhood' => 'Vila Industrial',
                    'state_id' => 35, // São Paulo
                    'city_id' => $saopaulo_id,
                ],
                [
                    'name' => 'Impressão Rápida 24h',
                    'email' => 'contato@impressao24h.com.br',
                    'phone' => '(21) 2233-4455',
                    'postal_code' => '20000-000',
                    'address' => 'Rua da Impressão, 88',
                    'neighborhood' => 'Centro',
                    'state_id' => 33, // Rio de Janeiro
                    'city_id' => $rio_id,
                ],
                [
                    'name' => 'Print Master Pro',
                    'email' => 'atendimento@printmasterpro.com',
                    'phone' => '(41) 3322-6677',
                    'postal_code' => '80000-000',
                    'address' => 'Rua das Artes Gráficas, 112',
                    'neighborhood' => 'Bigorrilho',
                    'state_id' => 41, // Paraná
                    'city_id' => $curitiba_id,
                ],
                [
                    'name' => 'Gráfica Digital Express',
                    'email' => 'suporte@graficaexpress.com.br',
                    'phone' => '(51) 3333-7777',
                    'postal_code' => '90440-090',
                    'address' => 'Avenida dos Impressos, 200',
                    'neighborhood' => 'Partenon',
                    'state_id' => 43, // Rio Grande do Sul
                    'city_id' => $portoalegre_id,
                ],
                [
                    'name' => 'ProPrint Gráfica',
                    'email' => 'proprint@proprintgrafica.com.br',
                    'phone' => '(11) 5555-9999',
                    'postal_code' => '06030-100',
                    'address' => 'Rua do Papel, 333',
                    'neighborhood' => 'Jardim Maria Tereza',
                    'state_id' => 35, // São Paulo
                    'city_id' => $osasco_id,
                ],
            ];
            

            $supplierIds = [];
            foreach ($suppliers as $supplierData) {
                $supplierData['tenant_id'] = $tenantId;
                $supplier = Supplier::create($supplierData);
                $supplierIds[] = $supplier->id;
            }

            Log::info('Creating supplies');

            // Suprimentos básicos
            $supplies = [
                [
                    'name' => 'Papel Sulfite A4 75g',
                    'description' => 'Papel sulfite branco A4 75g/m²',
                    'supplier_id' => $supplierIds[0],
                    'unit' => 'Resma',
                    'stock' => 50,
                    'min_stock' => 10,
                    'cost_price' => 25.90,
                ],
                [
                    'name' => 'Papel Couché 170g A3',
                    'description' => 'Papel couché brilho 170g/m² formato A3',
                    'supplier_id' => $supplierIds[0],
                    'unit' => 'Pacote',
                    'stock' => 20,
                    'min_stock' => 5,
                    'cost_price' => 45.90,
                ],
                [
                    'name' => 'Toner HP CF258A',
                    'description' => 'Toner HP LaserJet Pro M428fdw',
                    'supplier_id' => $supplierIds[1],
                    'unit' => 'Unidade',
                    'stock' => 5,
                    'min_stock' => 2,
                    'cost_price' => 289.90,
                ],
                [
                    'name' => 'Papel Fotográfico Glossy A4 180g',
                    'description' => 'Papel fotográfico gloss A4 180g/m² para impressões de alta qualidade',
                    'supplier_id' => $supplierIds[2],
                    'unit' => 'Pacote',
                    'stock' => 30,
                    'min_stock' => 8,
                    'cost_price' => 79.90,
                ],
                [
                    'name' => 'Cartucho de Tinta Epson 664',
                    'description' => 'Cartucho de tinta Epson 664 Preto',
                    'supplier_id' => $supplierIds[3],
                    'unit' => 'Unidade',
                    'stock' => 12,
                    'min_stock' => 4,
                    'cost_price' => 38.90,
                ],
                [
                    'name' => 'Adesivo Vinil Glossy 1,20m x 50m',
                    'description' => 'Adesivo vinil glossy para impressão em grandes formatos',
                    'supplier_id' => $supplierIds[4],
                    'unit' => 'Rolo',
                    'stock' => 15,
                    'min_stock' => 3,
                    'cost_price' => 120.00,
                ],
                [
                    'name' => 'Laminação A4 Fosca',
                    'description' => 'Folhas de laminação fosca tamanho A4',
                    'supplier_id' => $supplierIds[5],
                    'unit' => 'Pacote',
                    'stock' => 10,
                    'min_stock' => 3,
                    'cost_price' => 49.90,
                ],
                [
                    'name' => 'Cinta para Encadernação 25mm',
                    'description' => 'Cinta de encadernação preta 25mm para acabamento de documentos',
                    'supplier_id' => $supplierIds[0],
                    'unit' => 'Pacote',
                    'stock' => 25,
                    'min_stock' => 5,
                    'cost_price' => 19.90,
                ],
                [
                    'name' => 'Placa de PVC 3mm',
                    'description' => 'Placa de PVC 3mm para corte e personalização',
                    'supplier_id' => $supplierIds[1],
                    'unit' => 'Unidade',
                    'stock' => 8,
                    'min_stock' => 2,
                    'cost_price' => 65.00,
                ],
                [
                    'name' => 'Filme para Impressão UV',
                    'description' => 'Filme fotográfico para impressão UV em materiais rígidos',
                    'supplier_id' => $supplierIds[2],
                    'unit' => 'Rolo',
                    'stock' => 10,
                    'min_stock' => 3,
                    'cost_price' => 150.00,
                ],
            ];
            

            foreach ($supplies as $supplyData) {
                $supplyData['tenant_id'] = $tenantId;
                Supply::create($supplyData);
            }

            Log::info('Creating products');

            // Produtos básicos
            $products = [
                [
                    'name' => 'Cartão de Visita 4x4',
                    'description' => 'Cartão de visita colorido frente e verso em papel couché 300g',
                    'category_id' => $categoryIds['Papelaria']['id'],
                    'subcategory_id' => $categoryIds['Papelaria']['subcategories']['Cartões de Visita'],
                    'format' => '9x5cm',
                    'material' => 'Papel Couché 300g',
                    'finishing' => 'Laminação Fosca',
                    'production_time' => 2,
                    'min_quantity' => 100,
                    'base_price' => 90.00,
                    'customization_options' => json_encode([
                        'cores' => ['4x4', '4x0'],
                        'acabamentos' => ['Laminação Fosca', 'Laminação Brilho', 'Sem Laminação'],
                    ]),
                    'file_requirements' => json_encode([
                        'formato' => 'PDF, JPG, PNG',
                        'resolucao' => '300 DPI',
                        'cores' => 'CMYK',
                        'sangria' => '2mm',
                    ]),
                ],
                [
                    'name' => 'Banner Lona 440g',
                    'description' => 'Banner em lona 440g com acabamento',
                    'category_id' => $categoryIds['Impressão Digital']['id'],
                    'subcategory_id' => $categoryIds['Impressão Digital']['subcategories']['Grandes Formatos'],
                    'format' => 'Personalizado',
                    'material' => 'Lona 440g',
                    'finishing' => 'Bastão e Corda',
                    'production_time' => 1,
                    'min_quantity' => 1,
                    'base_price' => 45.00,
                    'customization_options' => json_encode([
                        'acabamentos' => ['Bastão e Corda', 'Ilhós', 'Sem acabamento'],
                        'tamanhos' => ['60x80cm', '80x120cm', '90x150cm', 'Personalizado'],
                    ]),
                    'file_requirements' => json_encode([
                        'formato' => 'PDF, JPG, PNG',
                        'resolucao' => '150 DPI',
                        'cores' => 'CMYK',
                        'sangria' => '5mm',
                    ]),
                ],
                [
                    'name' => 'Adesivo Vinil Personalizado',
                    'description' => 'Adesivo vinil adesivo com impressão digital, resistente ao tempo',
                    'category_id' => $categoryIds['Impressão Digital']['id'],
                    'subcategory_id' => $categoryIds['Impressão Digital']['subcategories']['Materiais Promocionais'],
                    'format' => 'Personalizado',
                    'material' => 'Vinil',
                    'finishing' => 'Corte Personalizado',
                    'production_time' => 3,
                    'min_quantity' => 10,
                    'base_price' => 120.00,
                    'customization_options' => json_encode([
                        'acabamentos' => ['Sem acabamento', 'Corte Personalizado'],
                        'tamanhos' => ['A4', 'A3', 'Customizado'],
                    ]),
                    'file_requirements' => json_encode([
                        'formato' => 'PDF, JPG, PNG',
                        'resolucao' => '300 DPI',
                        'cores' => 'CMYK',
                        'sangria' => '3mm',
                    ]),
                ],
                [
                    'name' => 'Flyer A5 150g',
                    'description' => 'Flyer impresso em papel 150g, com acabamento brilho ou fosco',
                    'category_id' => $categoryIds['Impressão Digital']['id'],
                    'subcategory_id' => $categoryIds['Impressão Digital']['subcategories']['Materiais Promocionais'],
                    'format' => 'A5',
                    'material' => 'Papel Couché 150g',
                    'finishing' => 'Laminação Fosca',
                    'production_time' => 1,
                    'min_quantity' => 200,
                    'base_price' => 50.00,
                    'customization_options' => json_encode([
                        'acabamentos' => ['Laminação Fosca', 'Laminação Brilho', 'Sem Laminação'],
                        'cores' => ['4x4', '4x0'],
                    ]),
                    'file_requirements' => json_encode([
                        'formato' => 'PDF, JPG, PNG',
                        'resolucao' => '300 DPI',
                        'cores' => 'CMYK',
                        'sangria' => '2mm',
                    ]),
                ],
                [
                    'name' => 'Roll-Up 85x200cm',
                    'description' => 'Roll-up personalizado para eventos, impresso em lona',
                    'category_id' => $categoryIds['Impressão Digital']['id'],
                    'subcategory_id' => $categoryIds['Impressão Digital']['subcategories']['Grandes Formatos'],
                    'format' => '85x200cm',
                    'material' => 'Lona 440g',
                    'finishing' => 'Estrutura e Sacola',
                    'production_time' => 2,
                    'min_quantity' => 1,
                    'base_price' => 190.00,
                    'customization_options' => json_encode([
                        'acabamentos' => ['Estrutura e Sacola', 'Sem acabamento'],
                        'tamanhos' => ['85x200cm'],
                    ]),
                    'file_requirements' => json_encode([
                        'formato' => 'PDF, JPG, PNG',
                        'resolucao' => '150 DPI',
                        'cores' => 'CMYK',
                        'sangria' => '5mm',
                    ]),
                ],
                [
                    'name' => 'Folder Tri-Fold A4',
                    'description' => 'Folder tri-fold impresso em papel couché 170g',
                    'category_id' => $categoryIds['Impressão Digital']['id'],
                    'subcategory_id' => $categoryIds['Impressão Digital']['subcategories']['Materiais Promocionais'],
                    'format' => 'A4',
                    'material' => 'Papel Couché 170g',
                    'finishing' => 'Vinco e Dobra',
                    'production_time' => 3,
                    'min_quantity' => 50,
                    'base_price' => 75.00,
                    'customization_options' => json_encode([
                        'acabamentos' => ['Vinco e Dobra', 'Sem acabamento'],
                        'cores' => ['4x4', '4x0'],
                    ]),
                    'file_requirements' => json_encode([
                        'formato' => 'PDF, JPG, PNG',
                        'resolucao' => '300 DPI',
                        'cores' => 'CMYK',
                        'sangria' => '2mm',
                    ]),
                ],
            ];
            

            foreach ($products as $productData) {
                $productData['tenant_id'] = $tenantId;
                $productData['is_active'] = true;
                Product::create($productData);
            }

            DB::commit();
            Log::info('TenantSeeder completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in TenantSeeder', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 