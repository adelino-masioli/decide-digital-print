<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'zip_code',
                'address',
                'number',
                'complement',
                'neighborhood',
                'city',
                'state'
            ]);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('zip_code');
            $table->string('address');
            $table->string('number');
            $table->string('complement')->nullable();
            $table->string('neighborhood');
            $table->string('city');
            $table->string('state');
        });
    }
}; 