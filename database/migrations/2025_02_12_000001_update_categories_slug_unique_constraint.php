<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remove a restrição única existente
            $table->dropUnique('categories_slug_unique');
            
            // Adiciona uma nova restrição única composta com tenant_id
            $table->unique(['tenant_id', 'slug'], 'categories_tenant_id_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remove a restrição única composta
            $table->dropUnique('categories_tenant_id_slug_unique');
            
            // Restaura a restrição única original
            $table->unique('slug', 'categories_slug_unique');
        });
    }
}; 