<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_info')->nullable();
            $table->string('email');
            $table->string('phone', 20);
            $table->string('postal_code', 9);
            $table->string('address')->nullable();
            $table->string('neighborhood');
            $table->foreignId('state_id')->constrained();
            $table->foreignId('city_id')->constrained();
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
}; 