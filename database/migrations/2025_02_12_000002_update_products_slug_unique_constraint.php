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
            $table->dropUnique('products_slug_unique');
            
            // Adiciona uma nova restrição única composta com tenant_id
            $table->unique(['tenant_id', 'slug'], 'products_tenant_id_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove a restrição única composta
            $table->dropUnique('products_tenant_id_slug_unique');
            
            // Restaura a restrição única original
            $table->unique('slug', 'products_slug_unique');
        });
    }
}; 