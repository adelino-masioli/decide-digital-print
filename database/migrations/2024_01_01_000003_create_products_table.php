<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('subcategory_id')->nullable()->constrained('categories')->onDelete('restrict');
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->string('keywords')->nullable();
            $table->string('format')->nullable();
            $table->string('material')->nullable();
            $table->string('weight')->nullable();
            $table->string('finishing')->nullable();
            $table->string('color')->nullable();
            $table->integer('production_time')->nullable();
            $table->integer('min_quantity')->default(1);
            $table->integer('max_quantity')->nullable();
            $table->json('customization_options')->nullable();
            $table->json('file_requirements')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Índices
            $table->index('tenant_id');
            $table->index('category_id');
            $table->index('slug');
            $table->index('sku');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}; 