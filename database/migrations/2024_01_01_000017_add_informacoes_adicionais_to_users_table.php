<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona os campos de informações adicionais, se não existirem
            $table->string('company_name')->nullable()->after('is_tenant_admin');
            $table->string('trading_name')->nullable()->after('company_name');
            $table->string('state_registration')->nullable()->after('trading_name');
            $table->string('municipal_registration')->nullable()->after('state_registration');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'trading_name',
                'state_registration',
                'municipal_registration'
            ]);
        });
    }
}; 