<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('quote_id')->constrained();
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users');
            $table->enum('status', [
                'pending_payment',
                'processing',
                'in_production',
                'completed',
                'canceled'
            ])->default('pending_payment');
            $table->enum('payment_method', [
                'cash',
                'credit_card',
                'pix',
                'bank_slip'
            ])->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 