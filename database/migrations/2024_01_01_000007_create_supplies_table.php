<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('supplier_id')->constrained();
            $table->string('unit', 50);
            $table->decimal('stock', 10, 2);
            $table->decimal('min_stock', 10, 2);
            $table->decimal('cost_price', 10, 2);
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('supplier_id');
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplies');
    }
}; 