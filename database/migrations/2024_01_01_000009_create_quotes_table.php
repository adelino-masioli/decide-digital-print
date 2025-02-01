<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['draft', 'open', 'approved', 'expired', 'converted', 'canceled'])
                ->default('draft');
            $table->date('valid_until');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('version_history')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index('valid_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
}; 