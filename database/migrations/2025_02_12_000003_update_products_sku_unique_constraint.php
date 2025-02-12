<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove a restrição única existente
            $table->dropUnique('products_sku_unique');
            
            // Adiciona uma nova restrição única composta com tenant_id
            $table->unique(['tenant_id', 'sku'], 'products_tenant_id_sku_unique');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove a restrição única composta
            $table->dropUnique('products_tenant_id_sku_unique');
            
            // Restaura a restrição única original
            $table->unique('sku', 'products_sku_unique');
        });
    }
}; 