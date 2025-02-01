<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona os campos para informações adicionais (para usuários admin)
            $table->string('company_address')->nullable()->after('municipal_registration');
            $table->string('company_logo')->nullable()->after('company_address');
            $table->decimal('company_latitude', 10, 7)->nullable()->after('company_logo');
            $table->decimal('company_longitude', 10, 7)->nullable()->after('company_latitude');
            $table->text('seo_text')->nullable()->after('company_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_address',
                'company_logo',
                'company_latitude',
                'company_longitude',
                'seo_text'
            ]);
        });
    }
}; 