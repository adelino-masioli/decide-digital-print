<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('opportunity_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->json('customization_options')->nullable(); // Opções de Personalização
            $table->json('file_requirements')->nullable(); // Requisitos do Arquivo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('opportunity_items');
    }
}; 