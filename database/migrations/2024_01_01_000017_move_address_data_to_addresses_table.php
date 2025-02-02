<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Address;
use App\Models\State;
use App\Models\City;

return new class extends Migration
{
    public function up()
    {
        // Primeiro, vamos criar um backup dos dados de endereço
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            // Encontrar ou criar o estado
            $state = State::firstOrCreate(['name' => $user->state ?? '']);
            
            // Encontrar ou criar a cidade
            $city = City::firstOrCreate(
                ['name' => $user->city ?? ''],
                ['state_id' => $state->id]
            );

            // Criar o endereço
            Address::create([
                'user_id' => $user->id,
                'zip_code' => $user->zip_code ?? '',
                'street' => $user->address ?? '',  // Se não existir, usa string vazia
                'number' => $user->number ?? '',
                'complement' => $user->complement,
                'neighborhood' => $user->neighborhood ?? '',
                'state_id' => $state->id,
                'city_id' => $city->id,
            ]);
        }
    }

    public function down()
    {
        Address::truncate();
    }
}; 