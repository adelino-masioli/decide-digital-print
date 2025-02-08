<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('guard_name');
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->after('guard_name');
            $table->foreign('tenant_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        Schema::table($tableNames['permissions'], function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });

        Schema::table($tableNames['roles'], function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
        });
    }
}; 