<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $states = [
            [
                "id" => 11,
                "uf" => "RO",
                "name" => "Rondônia",
            ],
            [
                "id" => 12,
                "uf" => "AC",
                "name" => "Acre",
            ],
            [
                "id" => 13,
                "uf" => "AM",
                "name" => "Amazonas",
            ],
            [
                "id" => 14,
                "uf" => "RR",
                "name" => "Roraima",
            ],
            [
                "id" => 15,
                "uf" => "PA",
                "name" => "Pará",
            ],
            [
                "id" => 16,
                "uf" => "AP",
                "name" => "Amapá",
            ],
            [
                "id" => 17,
                "uf" => "TO",
                "name" => "Tocantins",
            ],
            [
                "id" => 21,
                "uf" => "MA",
                "name" => "Maranhão",
            ],
            [
                "id" => 22,
                "uf" => "PI",
                "name" => "Piauí",
            ],
            [
                "id" => 23,
                "uf" => "CE",
                "name" => "Ceará",
            ],
            [
                "id" => 24,
                "uf" => "RN",
                "name" => "Rio Grande do Norte",
            ],
            [
                "id" => 25,
                "uf" => "PB",
                "name" => "Paraíba",
            ],
            [
                "id" => 26,
                "uf" => "PE",
                "name" => "Pernambuco",
            ],
            [
                "id" => 27,
                "uf" => "AL",
                "name" => "Alagoas",
            ],
            [
                "id" => 28,
                "uf" => "SE",
                "name" => "Sergipe",
            ],
            [
                "id" => 29,
                "uf" => "BA",
                "name" => "Bahia",
            ],
            [
                "id" => 31,
                "uf" => "MG",
                "name" => "Minas Gerais",
            ],
            [
                "id" => 32,
                "uf" => "ES",
                "name" => "Espírito Santo",
            ],
            [
                "id" => 33,
                "uf" => "RJ",
                "name" => "Rio de Janeiro",
            ],
            [
                "id" => 35,
                "uf" => "SP",
                "name" => "São Paulo",
            ],
            [
                "id" => 41,
                "uf" => "PR",
                "name" => "Paraná",
            ],
            [
                "id" => 42,
                "uf" => "SC",
                "name" => "Santa Catarina",
            ],
            [
                "id" => 43,
                "uf" => "RS",
                "name" => "Rio Grande do Sul",
            ],
            [
                "id" => 50,
                "uf" => "MS",
                "name" => "Mato Grosso do Sul",
            ],
            [
                "id" => 51,
                "uf" => "MT",
                "name" => "Mato Grosso",
            ],
            [
                "id" => 52,
                "uf" => "GO",
                "name" => "Goiás",
            ],
            [
                "id" => 53,
                "uf" => "DF",
                "name" => "Distrito Federal",
            ],
        ];

        foreach ($states as $stateData) {
            State::updateOrCreate(
                ['id' => $stateData['id']], // The unique identifier
                [
                    'uf' => $stateData['uf'],
                    'name' => $stateData['name'],
                ]
            );
        }
    }
} 