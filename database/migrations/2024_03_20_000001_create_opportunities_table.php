<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('users');
            $table->foreignId('responsible_id')->constrained('users'); // vendedor/responsável
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('value', 10, 2);
            $table->string('status')->default('lead'); // lead, negotiation, proposal, won, lost
            $table->date('expected_closure_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('opportunities');
    }
}; 